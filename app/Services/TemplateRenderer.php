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

        $normalized = $this->normalizeSimpleVariables((string) $content);

        return Blade::render(
            $normalized,
            $this->withFallbackVariables($normalized, $variables),
            deleteCachedView: true,
        );
    }

    private function normalizeSimpleVariables(string $content): string
    {
        return preg_replace('/{{\s*([A-Za-z_][A-Za-z0-9_]*)\s*}}/', '{{ \$$1 }}', $content) ?? $content;
    }

    private function withFallbackVariables(string $normalizedContent, array $variables): array
    {
        preg_match_all('/{{\s*\$([A-Za-z_][A-Za-z0-9_]*)\s*}}/', $normalizedContent, $matches);
        $names = array_unique($matches[1] ?? []);

        foreach ($names as $name) {
            if (! array_key_exists($name, $variables)) {
                $variables[$name] = '['.$name.']';
            }
        }

        return $variables;
    }
}
