<?php

namespace App\Actions;

use App\DTOs\MailSendData;
use App\Enums\MailLogStatus;
use App\Models\ApiConsumer;
use App\Models\MailLog;
use App\Models\MailTemplate;

class CreateMailLogAction
{
    public function execute(MailTemplate $template, MailSendData $data, string $recipient, ?ApiConsumer $consumer, string $subject): MailLog
    {
        return MailLog::query()->create([
            'api_consumer_id' => $consumer?->id,
            'mail_type_id' => $template->mail_type_id,
            'recipient' => $recipient,
            'subject' => $subject,
            'payload' => [
                'type' => $data->type,
                'to' => $data->to,
                'cc' => $data->cc,
                'bcc' => $data->bcc,
                'variables' => $data->variables,
                'attachments' => $data->attachments,
            ],
            'status' => MailLogStatus::Queued,
        ]);
    }
}
