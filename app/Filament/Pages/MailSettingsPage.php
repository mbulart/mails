<?php

namespace App\Filament\Pages;

use App\Models\MailSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailSettingsPage extends Page
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings SMTP';

    protected static ?string $title = 'Configuration SMTP';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected string $view = 'filament.pages.mail-settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(MailSetting::values());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Sections settings')
                    ->persistTabInQueryString('settings')
                    ->tabs([
                        Tab::make('SMTP')
                            ->icon('heroicon-o-server-stack')
                            ->schema([
                                Section::make('Serveur SMTP')
                                    ->description('Paramètres de connexion au serveur sortant.')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('mail_mailer')
                                            ->label('Mailer')
                                            ->options([
                                                'smtp' => 'SMTP',
                                                'log' => 'Log',
                                                'array' => 'Array',
                                            ])
                                            ->required(),
                                        TextInput::make('mail_host')->label('Host')->required(),
                                        TextInput::make('mail_port')->label('Port')->numeric()->required(),
                                        Select::make('mail_encryption')
                                            ->label('Encryption')
                                            ->options([
                                                '' => 'Aucune',
                                                'tls' => 'TLS',
                                                'ssl' => 'SSL',
                                            ]),
                                    ]),
                            ]),
                        Tab::make('Identifiants')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Section::make('Authentification')
                                    ->description('Ces valeurs sont stockées chiffrées en base.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('mail_username')->label('Username'),
                                        TextInput::make('mail_password')->label('Password')->password()->revealable(),
                                    ]),
                            ]),
                        Tab::make('Expéditeur')
                            ->icon('heroicon-o-at-symbol')
                            ->schema([
                                Section::make('Adresse expéditeur')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('mail_from_address')->label('Email expéditeur')->email()->required(),
                                        TextInput::make('mail_from_name')->label('Nom expéditeur')->required(),
                                    ]),
                            ]),
                        Tab::make('Tests')
                            ->icon('heroicon-o-paper-airplane')
                            ->schema([
                                Section::make('Validation')
                                    ->description('Sauvegarde, test TCP SMTP et envoi d\'un mail de test.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('test_email')->label('Email de test')->email()->dehydrated(false),
                                        Toggle::make('cache_after_save')->label('Recharger la configuration après sauvegarde')->default(true)->dehydrated(false),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Tester la connexion')
                ->icon('heroicon-o-signal')
                ->action('testConnection'),
            Action::make('sendTestEmail')
                ->label('Envoyer un email de test')
                ->icon('heroicon-o-paper-airplane')
                ->action('sendTestEmail'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        unset($data['test_email'], $data['cache_after_save']);

        MailSetting::saveValues($data);

        Notification::make()
            ->title('Configuration SMTP sauvegardée')
            ->success()
            ->send();
    }

    public function testConnection(): void
    {
        $data = $this->form->getState();
        $host = (string) ($data['mail_host'] ?? '');
        $port = (int) ($data['mail_port'] ?? 0);

        try {
            $connection = @stream_socket_client("tcp://{$host}:{$port}", $errno, $error, 8);

            if (! $connection) {
                throw new \RuntimeException($error ?: 'Connexion impossible.');
            }

            fclose($connection);

            Notification::make()->title('Connexion SMTP réussie')->success()->send();
        } catch (Throwable $exception) {
            Notification::make()->title('Connexion SMTP échouée')->body($exception->getMessage())->danger()->send();
        }
    }

    public function sendTestEmail(): void
    {
        $data = $this->form->getState();
        $email = $data['test_email'] ?? null;

        if (blank($email)) {
            Notification::make()->title('Renseigne un email de test')->warning()->send();

            return;
        }

        try {
            MailSetting::saveValues(collect($data)->except(['test_email', 'cache_after_save'])->all());
            Mail::raw('Email de test SMTP envoyé depuis Filament.', fn ($message) => $message->to($email)->subject('Test SMTP'));

            Notification::make()->title('Email de test envoyé')->success()->send();
        } catch (Throwable $exception) {
            Notification::make()->title('Envoi impossible')->body($exception->getMessage())->danger()->send();
        }
    }
}
