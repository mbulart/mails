<?php

namespace Database\Seeders;

use App\Models\MailType;
use Illuminate\Database\Seeder;

class MailTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Email de bienvenue', 'slug' => 'welcome_email', 'description' => 'Message envoyé lors de la création de compte.'],
            ['name' => 'Réinitialisation mot de passe', 'slug' => 'password_reset', 'description' => 'Lien ou code de réinitialisation.'],
            ['name' => 'Facture créée', 'slug' => 'invoice_created', 'description' => 'Notification de facture générée.'],
            ['name' => 'Code OTP', 'slug' => 'otp_code', 'description' => 'Code de validation temporaire.'],
            ['name' => 'Newsletter', 'slug' => 'newsletter', 'description' => 'Communication marketing ou produit.'],
        ];

        foreach ($types as $type) {
            MailType::query()->updateOrCreate(['slug' => $type['slug']], $type + ['is_active' => true]);
        }
    }
}
