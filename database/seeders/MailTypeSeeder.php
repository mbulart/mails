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
            ['name' => 'Confirmation de compte', 'slug' => 'account_confirmation', 'description' => 'Lien de confirmation ou validation de compte utilisateur.'],
            ['name' => 'Code 2FA', 'slug' => 'two_factor_code', 'description' => 'Code de sécurité à 6 chiffres pour authentification forte.'],
            ['name' => 'Code OTP', 'slug' => 'otp_code', 'description' => 'Code de validation temporaire à usage unique.'],
            ['name' => 'Réinitialisation mot de passe', 'slug' => 'password_reset', 'description' => 'Lien ou code de réinitialisation du mot de passe.'],
            ['name' => 'Facture créée', 'slug' => 'invoice_created', 'description' => 'Notification de facture générée avec montant et lien de paiement.'],
            ['name' => 'Notification système', 'slug' => 'system_notification', 'description' => 'Notification transactionnelle courte pour informer un utilisateur.'],
            ['name' => 'Alerte sécurité', 'slug' => 'security_alert', 'description' => 'Alerte en cas de connexion ou action sensible détectée.'],
            ['name' => 'Invitation utilisateur', 'slug' => 'user_invitation', 'description' => 'Invitation à rejoindre une organisation, un espace ou une application.'],
            ['name' => 'Newsletter', 'slug' => 'newsletter', 'description' => 'Communication marketing, produit ou annonce périodique.'],
        ];

        foreach ($types as $type) {
            MailType::query()->updateOrCreate(['slug' => $type['slug']], $type + ['is_active' => true]);
        }
    }
}
