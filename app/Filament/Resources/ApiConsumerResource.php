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
                    TextInput::make('api_key_preview')->label('Aperçu clé')->disabled()->dehydrated(false),
                    TextInput::make('rate_limit')->label('Limite/minute')->numeric()->minValue(1)->default(100)->required(),
                    Toggle::make('is_active')->label('Actif')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('api_key_preview')->label('Clé')->badge(),
                TextColumn::make('rate_limit')->label('Limite/min')->sortable(),
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
