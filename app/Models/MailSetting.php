<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MailSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'is_secret',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'encrypted',
            'is_secret' => 'boolean',
        ];
    }

    public static function defaults(): array
    {
        return [
            'app_name' => config('app.name', 'Mail API'),
            'app_locale' => config('app.locale', 'fr'),
            'app_timezone' => config('app.timezone', 'Africa/Kinshasa'),
            'mail_mailer' => config('mail.default', 'smtp'),
            'mail_host' => config('mail.mailers.smtp.host', '127.0.0.1'),
            'mail_port' => (string) config('mail.mailers.smtp.port', 2525),
            'mail_username' => config('mail.mailers.smtp.username'),
            'mail_password' => config('mail.mailers.smtp.password'),
            'mail_encryption' => env('MAIL_ENCRYPTION', config('mail.mailers.smtp.scheme')),
            'mail_from_address' => config('mail.from.address'),
            'mail_from_name' => config('mail.from.name'),
            'test_email' => null,
            'cache_after_save' => '1',
        ];
    }

    public static function values(): array
    {
        try {
            if (! Schema::hasTable('mail_settings')) {
                return self::defaults();
            }

            return array_replace(self::defaults(), self::query()->pluck('value', 'key')->all());
        } catch (Throwable) {
            return self::defaults();
        }
    }

    public static function saveValues(array $values): void
    {
        foreach ($values as $key => $value) {
            $storedValue = is_bool($value) ? ($value ? '1' : '0') : $value;

            self::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $storedValue !== null && $storedValue !== '' ? (string) $storedValue : null,
                    'is_secret' => $key === 'mail_password',
                ],
            );
        }

        self::applyToConfig($values);
    }

    public static function applyToConfig(?array $values = null): void
    {
        $values ??= self::values();

        config([
            'app.name' => $values['app_name'] ?: 'Mail API',
            'app.locale' => $values['app_locale'] ?: 'fr',
            'app.timezone' => $values['app_timezone'] ?: 'Africa/Kinshasa',
            'mail.default' => $values['mail_mailer'] ?: 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $values['mail_host'] ?: '127.0.0.1',
            'mail.mailers.smtp.port' => (int) ($values['mail_port'] ?: 2525),
            'mail.mailers.smtp.username' => $values['mail_username'] ?: null,
            'mail.mailers.smtp.password' => $values['mail_password'] ?: null,
            'mail.mailers.smtp.scheme' => self::smtpScheme($values['mail_encryption'] ?? null),
            'mail.from.address' => $values['mail_from_address'] ?: 'hello@example.com',
            'mail.from.name' => $values['mail_from_name'] ?: config('app.name'),
        ]);
    }

    private static function smtpScheme(?string $encryption): ?string
    {
        return match (strtolower((string) $encryption)) {
            'ssl', 'smtps' => 'smtps',
            'tls', 'starttls', 'smtp' => 'smtp',
            default => null,
        };
    }
}
