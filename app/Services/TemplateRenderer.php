<?php

namespace App\Services;

use Illuminate\Support\Facades\Blade;

class TemplateRenderer
{
    public function render(?string $content, array $variables): string
    {
        if (blank($content)) {
            return '';
        }

        return Blade::render($this->normalizeSimpleVariables((string) $content), $variables, deleteCachedView: true);
    }

    private function normalizeSimpleVariables(string $content): string
    {
        return preg_replace('/{{\s*([A-Za-z_][A-Za-z0-9_]*)\s*}}/', '{{ \$$1 }}', $content) ?? $content;
    }
}
