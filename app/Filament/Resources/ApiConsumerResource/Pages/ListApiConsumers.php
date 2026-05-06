<?php

namespace App\Filament\Resources\ApiConsumerResource\Pages;

use App\Filament\Resources\ApiConsumerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiConsumers extends ListRecords
{
    protected static string $resource = ApiConsumerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
