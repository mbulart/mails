<?php

namespace App\Filament\Resources;

use App\Enums\MailLogStatus;
use App\Filament\Resources\MailLogResource\Pages\ListMailLogs;
use App\Filament\Resources\MailLogResource\Pages\ViewMailLog;
use App\Models\MailLog;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MailLogResource extends Resource
{
    protected static ?string $model = MailLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Historique emails';

    protected static ?string $modelLabel = 'log email';

    protected static ?string $pluralModelLabel = 'logs emails';

    protected static string|\UnitEnum|null $navigationGroup = 'Monitoring';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Message')
                ->icon('heroicon-o-envelope-open')
                ->columns(2)
                ->schema([
                    TextInput::make('recipient')->label('Destinataire')->disabled(),
                    TextInput::make('subject')->label('Sujet')->disabled(),
                    TextInput::make('status')->label('Statut')->disabled(),
                    TextInput::make('sent_at')->label('Envoyé le')->disabled(),
                ]),
            Section::make('Payload')
                ->icon('heroicon-o-code-bracket')
                ->schema([
                    KeyValue::make('payload.variables')->label('Variables')->disabled(),
                    Textarea::make('error_message')->label('Erreur')->rows(5)->disabled()->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('recipient')->label('Destinataire')->searchable(),
                TextColumn::make('subject')->label('Sujet')->limit(40)->searchable(),
                TextColumn::make('mailType.name')->label('Type')->sortable(),
                TextColumn::make('apiConsumer.name')->label('Consommateur')->placeholder('Interne'),
                TextColumn::make('status')->label('Statut')->badge()->sortable(),
                TextColumn::make('sent_at')->label('Envoyé')->dateTime('d/m/Y H:i')->placeholder('Non envoyé'),
                TextColumn::make('created_at')->label('Créé')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(MailLogStatus::cases())->mapWithKeys(fn (MailLogStatus $status) => [$status->value => $status->value])->all()),
                SelectFilter::make('mail_type_id')->label('Type')->relationship('mailType', 'name')->searchable()->preload(),
                SelectFilter::make('api_consumer_id')->label('Consommateur')->relationship('apiConsumer', 'name')->searchable()->preload(),
                Filter::make('created_at')
                    ->label('Date')
                    ->schema([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date))),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                ])->label('Actions')->icon('heroicon-m-ellipsis-vertical'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailLogs::route('/'),
            'view' => ViewMailLog::route('/{record}'),
        ];
    }
}
