<?php

namespace App\Filament\Resources\ApiConsumerResource\Pages;

use App\Filament\Resources\ApiConsumerResource;
use App\Models\ApiConsumer;
use Filament\Resources\Pages\EditRecord;

class EditApiConsumer extends EditRecord
{
    protected static string $resource = ApiConsumerResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newKey = trim((string) ($this->form->getState()['new_api_key'] ?? ''));

        if (filled($newKey)) {
            $attrs = ApiConsumer::attributesFromPlainKey($newKey);
            $data['api_key_hash'] = $attrs['hash'];
            $data['api_key_preview'] = $attrs['preview'];
            $data['api_key_plain'] = $attrs['plain'];
        }

        return $data;
    }
}
