<?php

namespace App\Jobs;

use App\Enums\MailLogStatus;
use App\Mail\DynamicTemplatedMail;
use App\Models\MailLog;
use App\Models\MailSetting;
use App\Models\MailTemplate;
use App\Services\TemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $mailLogId,
        public readonly int $mailTemplateId,
        public readonly array $variables,
        public readonly array $cc = [],
        public readonly array $bcc = [],
        public readonly array $attachments = [],
    ) {}

    public function handle(TemplateRenderer $renderer): void
    {
        $log = MailLog::query()->findOrFail($this->mailLogId);
        $template = MailTemplate::query()->findOrFail($this->mailTemplateId);

        try {
            MailSetting::applyToConfig();
            $log->apiConsumer?->applySmtpConfig();
            Mail::purge('smtp');

            $attachments = $this->prepareAttachments($this->attachments);
            $mailable = new DynamicTemplatedMail(
                subjectLine: $renderer->render($template->subject, $this->variables),
                htmlBody: $renderer->render($template->html_content, $this->variables),
                textBody: $template->text_content ? $renderer->render($template->text_content, $this->variables) : null,
                attachmentsPayload: $attachments,
            );

            $mailer = Mail::to($log->recipient);

            if ($this->cc !== []) {
                $mailer->cc($this->cc);
            }

            if ($this->bcc !== []) {
                $mailer->bcc($this->bcc);
            }

            $mailer->send($mailable);

            $log->forceFill([
                'status' => MailLogStatus::Sent,
                'error_message' => null,
                'sent_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $log->forceFill([
                'status' => MailLogStatus::Failed,
                'error_message' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }
    }

    private function prepareAttachments(array $attachments): array
    {
        return collect($attachments)->map(function (array $attachment): array {
            if (($attachment['type'] ?? null) !== 'url') {
                return $attachment;
            }

            $response = Http::timeout(10)->get((string) $attachment['url']);
            $response->throw();

            return [
                'type' => 'base64',
                'name' => $attachment['name'] ?? basename(parse_url((string) $attachment['url'], PHP_URL_PATH) ?: 'attachment'),
                'mime' => $attachment['mime'] ?? $response->header('Content-Type'),
                'content' => base64_encode($response->body()),
            ];
        })->all();
    }
}
