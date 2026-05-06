<?php

namespace App\DTOs;

class MailSendData
{
    public function __construct(
        public readonly string $type,
        /** @var array<int, string> */
        public readonly array $to,
        /** @var array<int, string> */
        public readonly array $cc = [],
        /** @var array<int, string> */
        public readonly array $bcc = [],
        /** @var array<string, mixed> */
        public readonly array $variables = [],
        /** @var array<int, array<string, mixed>> */
        public readonly array $attachments = [],
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            type: (string) $payload['type'],
            to: array_values((array) $payload['to']),
            cc: array_values($payload['cc'] ?? []),
            bcc: array_values($payload['bcc'] ?? []),
            variables: $payload['variables'] ?? [],
            attachments: $payload['attachments'] ?? [],
        );
    }
}
