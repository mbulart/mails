<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_type_id',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function mailType(): BelongsTo
    {
        return $this->belongsTo(MailType::class);
    }

    /**
     * @param  array<int, array{name?: string, description?: string}|string>|array<string, string>|null  $definition
     * @return array<string, string>
     */
    public static function previewSampleVariables(?array $definition): array
    {
        if ($definition === null || $definition === []) {
            return [
                'app_name' => 'Nom application',
                'consumer_logo_url' => '',
            ];
        }

        $samples = [];
        $keys = array_keys($definition);
        $isList = $keys === range(0, count($definition) - 1);

        if (! $isList) {
            foreach ($definition as $name => $desc) {
                if (! is_string($name) || $name === '') {
                    continue;
                }
                $label = is_string($desc) && $desc !== '' ? '['.$desc.']' : '['.$name.']';
                $samples[$name] = $label;
            }
        } else {
            foreach ($definition as $item) {
                $name = is_array($item) ? ($item['name'] ?? null) : $item;
                if (! filled($name)) {
                    continue;
                }
                $name = (string) $name;
                $description = is_array($item) ? (string) ($item['description'] ?? '') : '';
                $samples[$name] = $description !== '' ? '['.$description.']' : '['.$name.']';
            }
        }

        $samples['app_name'] ??= 'Nom application';
        $samples['consumer_logo_url'] ??= '';

        return $samples;
    }
}
