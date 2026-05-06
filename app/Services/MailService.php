<?php

namespace App\Services;

use App\Actions\SendTemplatedMailAction;
use App\DTOs\MailSendData;
use App\Models\ApiConsumer;

class MailService
{
    public function __construct(private readonly SendTemplatedMailAction $sendTemplatedMail) {}

    public function queue(MailSendData $data, ?ApiConsumer $consumer = null): array
    {
        return $this->sendTemplatedMail->execute($data, $consumer);
    }
}
