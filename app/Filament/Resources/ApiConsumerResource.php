<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiConsumerResource\Pages\CreateApiConsumer;
use App\Filament\Resources\ApiConsumerResource\Pages\EditApiConsumer;
use App\Filament\Resources\ApiConsumerResource\Pages\ListApiConsumers;
use App\Filament\Resources\ApiConsumerResource\Pages\ViewApiConsumer;
use App\Models\ApiConsumer;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ApiConsumerResource extends Resource
{
    protected static ?string $model = ApiConsumer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Consommateurs API';

    protected static ?string $modelLabel = 'consommateur API';

    protected static ?string $pluralModelLabel = 'consommateurs API';

    protected static string|\UnitEnum|null $navigationGroup = 'Sécurité';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identité du consommateur')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Nom')->required()->maxLength(255),
                    TextInput::make('email')->label('Email')->email()->required()->maxLength(255),
                ]),
            Section::make('Sécurité API')
                ->description('La clé complète est affichée uniquement à la création ou à la rotation.')
                ->icon('heroicon-o-shield-check')
                ->columns(3)
                ->schema([
                    TextInput::make('api_key_plain')->label('Aperçu clé')->dehydrated(false),
                    TextInput::make('rate_limit')->label('Limite/minute')->numeric()->minValue(1)->default(100)->required(),
                    Toggle::make('is_active')->label('Actif')->default(true),
                ]),
            Section::make('SMTP dédié du consommateur')
                ->description('Si le host SMTP est renseigné, ce consommateur utilisera son propre SMTP à la place du SMTP global.')
                ->icon('heroicon-o-server-stack')
                ->columns(2)
                ->schema([
                    Select::make('smtp_mailer')
                        ->label('Mailer')
                        ->options([
                            'smtp' => 'SMTP',
                            'log' => 'Log',
                            'array' => 'Array',
                        ])
                        ->default('smtp'),
                    TextInput::make('smtp_host')
                        ->label('Host SMTP')
                        ->placeholder('mail.example.com'),
                    TextInput::make('smtp_port')
                        ->label('Port SMTP')
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('587'),
                    Select::make('smtp_encryption')
                        ->label('Encryption')
                        ->options([
                            '' => 'Aucune',
                            'TLS' => 'TLS',
                            'SSL' => 'SSL',
                            'tls' => 'TLS',
                            'ssl' => 'SSL',
                        ]),
                    TextInput::make('smtp_username')
                        ->label('Username SMTP')
                        ->email(),
                    TextInput::make('smtp_password')
                        ->label('Password SMTP')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (?string $state): bool => filled($state)),
                    TextInput::make('smtp_from_address')
                        ->label('Email expéditeur')
                        ->email(),
                    TextInput::make('smtp_from_name')
                        ->label('Nom expéditeur'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('api_key_preview')
                    ->label('Clé')
                    ->badge()
                    ->copyable()
                    ->copyableState(fn (ApiConsumer $record): ?string => $record->api_key_plain)
                    ->copyMessage('Clé copiée')
                    ->copyMessageDuration(1500),
                TextColumn::make('rate_limit')->label('Limite/min')->sortable(),
                IconColumn::make('has_custom_smtp')
                    ->label('SMTP dédié')
                    ->state(fn (ApiConsumer $record): bool => $record->hasCustomSmtp())
                    ->boolean(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('last_used_at')->label('Dernière utilisation')->dateTime('d/m/Y H:i')->placeholder('Jamais')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Actif'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('rotate_key')
                        ->label('Régénérer la clé')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (ApiConsumer $record): void {
                            $plain = $record->rotateApiKey();

                            Notification::make()
                                ->title('Nouvelle clé API générée')
                                ->body($plain)
                                ->success()
                                ->persistent()
                                ->send();
                        }),
                    DeleteAction::make(),
                ])->label('Actions')->icon('heroicon-m-ellipsis-vertical'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiConsumers::route('/'),
            'create' => CreateApiConsumer::route('/create'),
            'view' => ViewApiConsumer::route('/{record}'),
            'edit' => EditApiConsumer::route('/{record}/edit'),
        ];
    }
}
