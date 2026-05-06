<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ApiConsumer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'api_key_hash',
        'api_key_preview',
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
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'rate_limit' => 'integer',
            'smtp_port' => 'integer',
            'smtp_password' => 'encrypted',
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
        ])->save();

        return $key['plain'];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MailLog::class);
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
            'mail.mailers.smtp.scheme' => filled($this->smtp_encryption) ? strtolower((string) $this->smtp_encryption) : null,
            'mail.from.address' => $this->smtp_from_address ?: config('mail.from.address'),
            'mail.from.name' => $this->smtp_from_name ?: config('mail.from.name'),
        ]);
    }
}
