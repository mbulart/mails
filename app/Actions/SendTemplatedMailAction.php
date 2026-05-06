<?php

namespace App\Actions;

use App\DTOs\MailSendData;
use App\Jobs\SendMailJob;
use App\Models\ApiConsumer;
use App\Models\MailTemplate;
use App\Models\MailType;
use App\Services\TemplateRenderer;
use InvalidArgumentException;

class SendTemplatedMailAction
{
    public function __construct(
        private readonly ValidateMailVariablesAction $validateMailVariables,
        private readonly CreateMailLogAction $createMailLog,
    ) {}

    /**
     * @return array<int, int>
     */
    public function execute(MailSendData $data, ?ApiConsumer $consumer = null): array
    {
        $mailType = MailType::query()
            ->where('slug', $data->type)
            ->where('is_active', true)
            ->first();

        if (! $mailType) {
            throw new InvalidArgumentException('Type de mail invalide ou inactif.');
        }

        $template = MailTemplate::query()
            ->whereBelongsTo($mailType)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->latest('id')
            ->first();

        if (! $template) {
            throw new InvalidArgumentException('Aucun template actif disponible pour ce type de mail.');
        }

        $this->validateMailVariables->execute($template, $data->variables);

        $subject = app(TemplateRenderer::class)->render($template->subject, $data->variables);
        $logIds = [];

        foreach ($data->to as $recipient) {
            $log = $this->createMailLog->execute($template, $data, $recipient, $consumer, $subject);
            $logIds[] = $log->id;

            SendMailJob::dispatch($log->id, $template->id, $data->variables, $data->cc, $data->bcc, $data->attachments);
        }

        return $logIds;
    }
}
