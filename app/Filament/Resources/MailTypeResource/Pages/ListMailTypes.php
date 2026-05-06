<?php

namespace App\Filament\Resources\MailTypeResource\Pages;

use App\Filament\Resources\MailTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMailTypes extends ListRecords
{
    protected static string $resource = MailTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
