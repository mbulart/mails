<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use App\Models\MailType;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            'welcome_email' => [
                'subject' => 'Bienvenue {{name}}',
                'html_content' => '<h1>Bonjour {{name}}</h1><p>Bienvenue sur notre plateforme.</p>',
                'text_content' => 'Bonjour {{name}}, bienvenue sur notre plateforme.',
                'variables' => [['name' => 'name', 'description' => 'Nom du destinataire']],
            ],
            'otp_code' => [
                'subject' => 'Votre code OTP',
                'html_content' => '<h1>Bonjour {{name}}</h1><p>Votre code OTP est : <strong>{{otp}}</strong></p>',
                'text_content' => 'Bonjour {{name}}, votre code OTP est : {{otp}}',
                'variables' => [['name' => 'name'], ['name' => 'otp']],
            ],
        ];

        foreach ($templates as $slug => $payload) {
            $type = MailType::query()->where('slug', $slug)->first();

            if (! $type) {
                continue;
            }

            MailTemplate::query()->updateOrCreate(
                ['mail_type_id' => $type->id, 'is_default' => true],
                $payload + ['is_active' => true],
            );
        }
    }
}
