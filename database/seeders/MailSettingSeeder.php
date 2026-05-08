<?php

namespace Database\Seeders;

use App\Models\MailSetting;
use Illuminate\Database\Seeder;

class MailSettingSeeder extends Seeder
{
    public function run(): void
    {
        MailSetting::saveValues([
            'app_name' => env('APP_NAME', 'Mail API'),
            'app_locale' => env('APP_LOCALE', 'fr'),
            'app_timezone' => env('APP_TIMEZONE', 'Africa/Kinshasa'),
            'mail_mailer' => env('MAIL_MAILER', 'smtp'),
            'mail_host' => env('MAIL_HOST', '127.0.0.1'),
            'mail_port' => (string) env('MAIL_PORT', 587),
            'mail_username' => env('MAIL_USERNAME'),
            'mail_password' => env('MAIL_PASSWORD'),
            'mail_encryption' => env('MAIL_ENCRYPTION', env('MAIL_SCHEME', 'tls')),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'mail_from_name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Mail API')),
            'test_email' => env('MAIL_TEST_EMAIL'),
            'cache_after_save' => env('MAIL_CACHE_AFTER_SAVE', true) ? '1' : '0',
        ]);
    }
}
