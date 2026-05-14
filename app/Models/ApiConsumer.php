<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApiConsumer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'api_key_hash',
        'api_key_preview',
        'api_key_plain',
        'is_active',
        'rate_limit',
        'last_used_at',
        'smtp_mailer',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'logo_path',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'rate_limit' => 'integer',
            'smtp_port' => 'integer',
            'smtp_password' => 'encrypted',
            'api_key_plain' => 'encrypted',
        ];
    }

    public static function generatePlainApiKey(): string
    {
        return 'pmk_'.Str::random(48);
    }

    /**
     * @return array{plain:string, hash:string, preview:string}
     */
    public static function makeApiKey(): array
    {
        $plain = self::generatePlainApiKey();

        return [
            'plain' => $plain,
            'hash' => hash('sha256', $plain),
            'preview' => substr($plain, 0, 8).'...',
        ];
    }

    public static function findForPlainKey(?string $plainKey): ?self
    {
        if (blank($plainKey)) {
            return null;
        }

        return self::query()
            ->where('api_key_hash', hash('sha256', $plainKey))
            ->first();
    }

    public function rotateApiKey(): string
    {
        $key = self::makeApiKey();

        $this->forceFill([
            'api_key_hash' => $key['hash'],
            'api_key_preview' => $key['preview'],
            'api_key_plain' => $key['plain'],
        ])->save();

        return $key['plain'];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MailLog::class);
    }

    /**
     * URL absolue du logo pour les clients email (null si absent).
     */
    public function logoPublicUrl(): ?string
    {
        if (blank($this->logo_path)) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->logo_path)) {
            return null;
        }

        return url(Storage::disk('public')->url($this->logo_path));
    }

    protected static function booted(): void
    {
        static::updating(function (ApiConsumer $consumer): void {
            if ($consumer->isDirty('logo_path')) {
                $original = $consumer->getOriginal('logo_path');
                if (filled($original) && $original !== $consumer->logo_path && Storage::disk('public')->exists($original)) {
                    Storage::disk('public')->delete($original);
                }
            }
        });

        static::deleting(function (ApiConsumer $consumer): void {
            if (filled($consumer->logo_path) && Storage::disk('public')->exists($consumer->logo_path)) {
                Storage::disk('public')->delete($consumer->logo_path);
            }
        });
    }

    public function hasCustomSmtp(): bool
    {
        return filled($this->smtp_host);
    }

    public function applySmtpConfig(): void
    {
        if (! $this->hasCustomSmtp()) {
            return;
        }

        config([
            'mail.default' => $this->smtp_mailer ?: 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $this->smtp_host,
            'mail.mailers.smtp.port' => $this->smtp_port ?: 587,
            'mail.mailers.smtp.username' => $this->smtp_username ?: null,
            'mail.mailers.smtp.password' => $this->smtp_password ?: null,
            'mail.mailers.smtp.scheme' => $this->smtpScheme(),
            'mail.from.address' => $this->smtp_from_address ?: config('mail.from.address'),
            'mail.from.name' => $this->smtp_from_name ?: config('mail.from.name'),
        ]);
    }

    private function smtpScheme(): ?string
    {
        return match (strtolower((string) $this->smtp_encryption)) {
            'ssl', 'smtps' => 'smtps',
            'tls', 'starttls', 'smtp' => 'smtp',
            default => null,
        };
    }
}
