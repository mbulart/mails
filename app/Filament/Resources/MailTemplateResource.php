<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailTemplateResource\Pages\CreateMailTemplate;
use App\Filament\Resources\MailTemplateResource\Pages\EditMailTemplate;
use App\Filament\Resources\MailTemplateResource\Pages\ListMailTemplates;
use App\Filament\Resources\MailTemplateResource\Pages\ViewMailTemplate;
use App\Models\MailTemplate;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MailTemplateResource extends Resource
{
    protected static ?string $model = MailTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket-square';

    protected static ?string $navigationLabel = 'Templates';

    protected static ?string $modelLabel = 'template';

    protected static ?string $pluralModelLabel = 'templates';

    protected static string|\UnitEnum|null $navigationGroup = 'Emails';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Association')
                ->icon('heroicon-o-link')
                ->columns(2)
                ->schema([
                    Select::make('mail_type_id')
                        ->label('Type d\'email')
                        ->relationship('mailType', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('subject')
                        ->label('Sujet')
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make('Contenu')
                ->description('HTML Blade avec variables dynamiques, conditions et boucles.')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Textarea::make('html_content')
                        ->label('Contenu HTML')
                        ->rows(14)
                        ->required()
                        ->columnSpanFull(),
                    Textarea::make('text_content')
                        ->label('Version texte')
                        ->rows(6)
                        ->columnSpanFull(),
                ]),
            Section::make('Variables supportées')
                ->icon('heroicon-o-variable')
                ->schema([
                    KeyValue::make('variables')
                        ->label('Variables attendues')
                        ->keyLabel('Nom')
                        ->valueLabel('Description')
                        ->columnSpanFull(),
                ]),
            Section::make('Publication')
                ->icon('heroicon-o-check-badge')
                ->columns(2)
                ->schema([
                    Toggle::make('is_default')->label('Template par défaut'),
                    Toggle::make('is_active')->label('Actif')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mailType.name')->label('Type')->searchable()->sortable(),
                TextColumn::make('subject')->label('Sujet')->searchable()->limit(50),
                IconColumn::make('is_default')->label('Défaut')->boolean(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('updated_at')->label('Mis à jour')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('mail_type_id')->label('Type')->relationship('mailType', 'name')->searchable()->preload(),
                TernaryFilter::make('is_default')->label('Par défaut'),
                TernaryFilter::make('is_active')->label('Actif'),
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
            'index' => ListMailTemplates::route('/'),
            'create' => CreateMailTemplate::route('/create'),
            'view' => ViewMailTemplate::route('/{record}'),
            'edit' => EditMailTemplate::route('/{record}/edit'),
        ];
    }
}
