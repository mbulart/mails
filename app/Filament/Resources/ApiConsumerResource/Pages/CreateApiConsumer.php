<?php

namespace App\Filament\Resources\ApiConsumerResource\Pages;

use App\Filament\Resources\ApiConsumerResource;
use App\Models\ApiConsumer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateApiConsumer extends CreateRecord
{
    protected static string $resource = ApiConsumerResource::class;

    private ?string $plainApiKey = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $manual = trim((string) ($this->form->getState()['new_api_key'] ?? ''));

        if (filled($manual)) {
            $attrs = ApiConsumer::attributesFromPlainKey($manual);
            $this->plainApiKey = $attrs['plain'];
            $data['api_key_hash'] = $attrs['hash'];
            $data['api_key_preview'] = $attrs['preview'];
            $data['api_key_plain'] = $attrs['plain'];

            return $data;
        }

        $key = ApiConsumer::makeApiKey();
        $this->plainApiKey = $key['plain'];

        $data['api_key_hash'] = $key['hash'];
        $data['api_key_preview'] = $key['preview'];
        $data['api_key_plain'] = $key['plain'];

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Clé API générée')
            ->body((string) $this->plainApiKey)
            ->success()
            ->persistent()
            ->send();
    }
}
