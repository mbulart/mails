<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicTemplatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private readonly string $subjectLine,
        private readonly string $htmlBody,
        private readonly ?string $textBody = null,
        private readonly array $attachmentsPayload = [],
    ) {}

    public function build(): self
    {
        $mail = $this->subject($this->subjectLine)->html($this->htmlBody);

        foreach ($this->attachmentsPayload as $attachment) {
            $this->addAttachment($mail, $attachment);
        }

        return $mail;
    }

    private function addAttachment(self $mail, array $attachment): void
    {
        $name = $attachment['name'] ?? 'attachment';
        $mime = $attachment['mime'] ?? null;
        $options = array_filter(['mime' => $mime]);

        if (($attachment['type'] ?? null) === 'local' && filled($attachment['path'] ?? null)) {
            $mail->attach((string) $attachment['path'], array_filter(['as' => $name, 'mime' => $mime]));

            return;
        }

        if (($attachment['type'] ?? null) === 'base64' && filled($attachment['content'] ?? null)) {
            $mail->attachData(base64_decode((string) $attachment['content'], strict: true) ?: '', (string) $name, $options);
        }
    }
}
