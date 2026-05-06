<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailTypeResource\Pages\CreateMailType;
use App\Filament\Resources\MailTypeResource\Pages\EditMailType;
use App\Filament\Resources\MailTypeResource\Pages\ListMailTypes;
use App\Filament\Resources\MailTypeResource\Pages\ViewMailType;
use App\Models\MailType;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MailTypeResource extends Resource
{
    protected static ?string $model = MailType::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Types d\'emails';

    protected static ?string $modelLabel = 'type d\'email';

    protected static ?string $pluralModelLabel = 'types d\'emails';

    protected static string|\UnitEnum|null $navigationGroup = 'Emails';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations principales')
                ->description('Identité fonctionnelle du type d\'email utilisé par l\'API.')
                ->icon('heroicon-o-identification')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug((string) $state))),
                    TextInput::make('slug')
                        ->label('Slug API')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->alphaDash(),
                    Textarea::make('description')
                        ->label('Description')
                        ->columnSpanFull(),
                ]),
            Section::make('Statut')
                ->icon('heroicon-o-check-circle')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->badge()->searchable(),
                TextColumn::make('templates_count')->label('Templates')->counts('templates')->sortable(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('created_at')->label('Créé le')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Statut actif'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->label('Actions')->icon('heroicon-m-ellipsis-vertical'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailTypes::route('/'),
            'create' => CreateMailType::route('/create'),
            'view' => ViewMailType::route('/{record}'),
            'edit' => EditMailType::route('/{record}/edit'),
        ];
    }
}
