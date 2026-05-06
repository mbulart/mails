<?php

namespace App\Actions;

use App\Models\MailTemplate;
use InvalidArgumentException;

class ValidateMailVariablesAction
{
    public function execute(MailTemplate $template, array $variables): void
    {
        foreach ($template->variables ?? [] as $requiredVariable) {
            $name = is_array($requiredVariable) ? ($requiredVariable['name'] ?? null) : $requiredVariable;

            if (filled($name) && ! array_key_exists((string) $name, $variables)) {
                throw new InvalidArgumentException("Variable [{$name}] manquante pour ce template.");
            }
        }
    }
}
