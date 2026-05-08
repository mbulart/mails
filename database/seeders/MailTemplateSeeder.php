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
                'html_content' => $this->layout(
                    title: 'Bienvenue {{name}}',
                    preheader: 'Votre compte est prêt.',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>Bienvenue sur <strong>{{app_name}}</strong>. Votre compte est maintenant prêt et vous pouvez commencer à utiliser nos services.</p>
                        <div class="panel">
                            <p class="panel-title">Vos informations</p>
                            <p>Email : <strong>{{email}}</strong></p>
                        </div>
                    HTML,
                    buttonLabel: 'Accéder à mon espace',
                    buttonUrl: '{{login_url}}',
                ),
                'text_content' => 'Bonjour {{name}}, bienvenue sur {{app_name}}. Accédez à votre espace : {{login_url}}',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'app_name' => 'Nom de l’application',
                    'email' => 'Adresse email du destinataire',
                    'login_url' => 'Lien de connexion',
                ]),
            ],
            'account_confirmation' => [
                'subject' => 'Confirmez votre compte {{app_name}}',
                'html_content' => $this->layout(
                    title: 'Confirmez votre compte',
                    preheader: 'Un dernier clic pour activer votre compte.',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>Merci pour votre inscription. Cliquez sur le bouton ci-dessous pour confirmer votre adresse email et activer votre compte.</p>
                        <p class="muted">Si vous n’êtes pas à l’origine de cette demande, ignorez simplement ce message.</p>
                    HTML,
                    buttonLabel: 'Confirmer mon compte',
                    buttonUrl: '{{confirmation_url}}',
                ),
                'text_content' => 'Bonjour {{name}}, confirmez votre compte ici : {{confirmation_url}}',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'app_name' => 'Nom de l’application',
                    'confirmation_url' => 'Lien de confirmation du compte',
                ]),
            ],
            'two_factor_code' => [
                'subject' => 'Votre code de sécurité {{app_name}}',
                'html_content' => $this->layout(
                    title: 'Code de sécurité',
                    preheader: 'Votre code 2FA expire bientôt.',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>Utilisez le code ci-dessous pour finaliser votre connexion.</p>
                        <div class="code">{{code}}</div>
                        <p>Ce code à 6 chiffres expire dans <strong>{{expires_in}}</strong>.</p>
                        <p class="muted">Ne partagez jamais ce code. Notre équipe ne vous le demandera jamais.</p>
                    HTML,
                ),
                'text_content' => 'Bonjour {{name}}, votre code de sécurité est {{code}}. Il expire dans {{expires_in}}.',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'app_name' => 'Nom de l’application',
                    'code' => 'Code 2FA à 6 chiffres',
                    'expires_in' => 'Durée de validité, ex: 10 minutes',
                ]),
            ],
            'otp_code' => [
                'subject' => 'Votre code OTP',
                'html_content' => $this->layout(
                    title: 'Validation par OTP',
                    preheader: 'Votre code OTP est prêt.',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>Votre code de validation est :</p>
                        <div class="code">{{otp}}</div>
                        <p>Il expire dans <strong>{{expires_in}}</strong>.</p>
                    HTML,
                ),
                'text_content' => 'Bonjour {{name}}, votre code OTP est {{otp}}. Il expire dans {{expires_in}}.',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'otp' => 'Code OTP à usage unique',
                    'expires_in' => 'Durée de validité, ex: 5 minutes',
                ]),
            ],
            'password_reset' => [
                'subject' => 'Réinitialisation de votre mot de passe',
                'html_content' => $this->layout(
                    title: 'Réinitialiser votre mot de passe',
                    preheader: 'Une demande de réinitialisation a été reçue.',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.</p>
                        <p>Ce lien expire dans <strong>{{expires_in}}</strong>.</p>
                        <p class="muted">Si vous n’avez pas demandé cette opération, vous pouvez ignorer cet email.</p>
                    HTML,
                    buttonLabel: 'Réinitialiser mon mot de passe',
                    buttonUrl: '{{reset_url}}',
                ),
                'text_content' => 'Bonjour {{name}}, réinitialisez votre mot de passe ici : {{reset_url}}. Expire dans {{expires_in}}.',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'reset_url' => 'Lien de réinitialisation',
                    'expires_in' => 'Durée de validité du lien',
                ]),
            ],
            'invoice_created' => [
                'subject' => 'Facture {{invoice_number}} disponible',
                'html_content' => $this->layout(
                    title: 'Votre facture est disponible',
                    preheader: 'Une nouvelle facture a été générée.',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>Votre facture <strong>{{invoice_number}}</strong> est maintenant disponible.</p>
                        <div class="panel">
                            <p class="panel-title">Résumé</p>
                            <p>Montant : <strong>{{amount}}</strong></p>
                            <p>Date limite : <strong>{{due_date}}</strong></p>
                        </div>
                    HTML,
                    buttonLabel: 'Voir la facture',
                    buttonUrl: '{{invoice_url}}',
                ),
                'text_content' => 'Bonjour {{name}}, la facture {{invoice_number}} de {{amount}} est disponible : {{invoice_url}}',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'invoice_number' => 'Numéro de facture',
                    'amount' => 'Montant formaté',
                    'due_date' => 'Date limite de paiement',
                    'invoice_url' => 'Lien de consultation ou paiement',
                ]),
            ],
            'system_notification' => [
                'subject' => '{{title}}',
                'html_content' => $this->layout(
                    title: '{{title}}',
                    preheader: '{{summary}}',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>{{message}}</p>
                        @if($action_url)
                            <p>Vous pouvez consulter les détails via le bouton ci-dessous.</p>
                        @endif
                    HTML,
                    buttonLabel: '{{action_label}}',
                    buttonUrl: '{{action_url}}',
                ),
                'text_content' => 'Bonjour {{name}}, {{message}} {{action_url}}',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'title' => 'Titre de la notification',
                    'summary' => 'Résumé court',
                    'message' => 'Message principal',
                    'action_label' => 'Texte du bouton',
                    'action_url' => 'Lien optionnel',
                ]),
            ],
            'security_alert' => [
                'subject' => 'Alerte sécurité sur votre compte',
                'html_content' => $this->layout(
                    title: 'Alerte sécurité',
                    preheader: 'Une activité sensible a été détectée.',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>Nous avons détecté une activité sensible sur votre compte.</p>
                        <div class="panel">
                            <p class="panel-title">Détails</p>
                            <p>Action : <strong>{{event}}</strong></p>
                            <p>Adresse IP : <strong>{{ip_address}}</strong></p>
                            <p>Appareil : <strong>{{device}}</strong></p>
                            <p>Date : <strong>{{occurred_at}}</strong></p>
                        </div>
                        <p class="muted">Si vous reconnaissez cette activité, aucune action n’est nécessaire.</p>
                    HTML,
                    buttonLabel: 'Sécuriser mon compte',
                    buttonUrl: '{{security_url}}',
                ),
                'text_content' => 'Alerte sécurité : {{event}} depuis {{ip_address}} sur {{device}} le {{occurred_at}}. {{security_url}}',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'event' => 'Action détectée',
                    'ip_address' => 'Adresse IP',
                    'device' => 'Navigateur ou appareil',
                    'occurred_at' => 'Date et heure',
                    'security_url' => 'Lien de sécurisation du compte',
                ]),
            ],
            'user_invitation' => [
                'subject' => '{{inviter_name}} vous invite sur {{app_name}}',
                'html_content' => $this->layout(
                    title: 'Vous êtes invité',
                    preheader: 'Rejoignez {{organization_name}} sur {{app_name}}.',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p><strong>{{inviter_name}}</strong> vous invite à rejoindre <strong>{{organization_name}}</strong> sur {{app_name}}.</p>
                        <p>Cette invitation expire dans <strong>{{expires_in}}</strong>.</p>
                    HTML,
                    buttonLabel: 'Accepter l’invitation',
                    buttonUrl: '{{invitation_url}}',
                ),
                'text_content' => '{{inviter_name}} vous invite sur {{app_name}} : {{invitation_url}}',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'app_name' => 'Nom de l’application',
                    'inviter_name' => 'Nom de la personne qui invite',
                    'organization_name' => 'Nom de l’organisation',
                    'invitation_url' => 'Lien d’invitation',
                    'expires_in' => 'Durée de validité',
                ]),
            ],
            'newsletter' => [
                'subject' => '{{headline}}',
                'html_content' => $this->layout(
                    title: '{{headline}}',
                    preheader: '{{summary}}',
                    body: <<<'HTML'
                        <p>Bonjour {{name}},</p>
                        <p>{{intro}}</p>
                        <div class="panel">
                            <p class="panel-title">{{feature_title}}</p>
                            <p>{{feature_description}}</p>
                        </div>
                        <p class="muted">Vous recevez cet email car vous êtes inscrit à nos communications.</p>
                    HTML,
                    buttonLabel: '{{cta_label}}',
                    buttonUrl: '{{cta_url}}',
                ),
                'text_content' => '{{headline}} - {{summary}} {{cta_url}}',
                'variables' => $this->variables([
                    'name' => 'Nom du destinataire',
                    'headline' => 'Titre principal',
                    'summary' => 'Résumé court',
                    'intro' => 'Introduction',
                    'feature_title' => 'Titre du bloc mis en avant',
                    'feature_description' => 'Description du bloc mis en avant',
                    'cta_label' => 'Texte du bouton',
                    'cta_url' => 'Lien du bouton',
                ]),
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

    /**
     * @param  array<string, string>  $variables
     * @return array<int, array{name: string, description: string}>
     */
    private function variables(array $variables): array
    {
        return collect($variables)
            ->map(fn (string $description, string $name): array => [
                'name' => $name,
                'description' => $description,
            ])
            ->values()
            ->all();
    }

    private function layout(string $title, string $preheader, string $body, ?string $buttonLabel = null, ?string $buttonUrl = null): string
    {
        $button = '';

        if (filled($buttonLabel) && filled($buttonUrl)) {
            $button = <<<HTML
                <table role="presentation" cellpadding="0" cellspacing="0" class="button-wrap">
                    <tr>
                        <td>
                            <a href="{$buttonUrl}" class="button">{$buttonLabel}</a>
                        </td>
                    </tr>
                </table>
            HTML;
        }

        return <<<HTML
            <!doctype html>
            <html lang="fr">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>{$title}</title>
                <style>
                    body {
                        margin: 0;
                        padding: 0;
                        background: #f3f4f6;
                        color: #111827;
                        font-family: Arial, Helvetica, sans-serif;
                    }
                    .preheader {
                        display: none;
                        max-height: 0;
                        overflow: hidden;
                        opacity: 0;
                    }
                    .wrapper {
                        width: 100%;
                        padding: 32px 16px;
                        background: #f3f4f6;
                    }
                    .card {
                        max-width: 640px;
                        margin: 0 auto;
                        overflow: hidden;
                        border-radius: 20px;
                        background: #ffffff;
                        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.10);
                    }
                    .header {
                        padding: 32px;
                        background: linear-gradient(135deg, #0f172a, #2563eb);
                        color: #ffffff;
                    }
                    .brand {
                        margin: 0 0 12px;
                        font-size: 13px;
                        font-weight: 700;
                        letter-spacing: 0.12em;
                        text-transform: uppercase;
                        opacity: 0.8;
                    }
                    h1 {
                        margin: 0;
                        font-size: 28px;
                        line-height: 1.25;
                    }
                    .content {
                        padding: 32px;
                        font-size: 16px;
                        line-height: 1.7;
                    }
                    .panel {
                        margin: 24px 0;
                        padding: 18px 20px;
                        border: 1px solid #dbeafe;
                        border-radius: 14px;
                        background: #eff6ff;
                    }
                    .panel-title {
                        margin: 0 0 8px;
                        color: #1d4ed8;
                        font-size: 13px;
                        font-weight: 700;
                        text-transform: uppercase;
                    }
                    .code {
                        margin: 24px 0;
                        padding: 18px;
                        border-radius: 16px;
                        background: #0f172a;
                        color: #ffffff;
                        font-size: 36px;
                        font-weight: 800;
                        letter-spacing: 0.24em;
                        text-align: center;
                    }
                    .button-wrap {
                        margin: 28px 0 8px;
                    }
                    .button {
                        display: inline-block;
                        padding: 14px 22px;
                        border-radius: 999px;
                        background: #2563eb;
                        color: #ffffff !important;
                        font-weight: 700;
                        text-decoration: none;
                    }
                    .muted {
                        color: #6b7280;
                        font-size: 14px;
                    }
                    .footer {
                        padding: 20px 32px 32px;
                        color: #6b7280;
                        font-size: 12px;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <span class="preheader">{$preheader}</span>
                <div class="wrapper">
                    <div class="card">
                        <div class="header">
                            <p class="brand">{{app_name}}</p>
                            <h1>{$title}</h1>
                        </div>
                        <div class="content">
                            {$body}
                            {$button}
                        </div>
                        <div class="footer">
                            Email automatique envoyé par {{app_name}}. Merci de ne pas répondre directement à ce message.
                        </div>
                    </div>
                </div>
            </body>
            </html>
        HTML;
    }
}
