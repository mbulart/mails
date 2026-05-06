<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit" icon="heroicon-o-check-circle">
            Sauvegarder la configuration
        </x-filament::button>
    </form>
</x-filament-panels::page>
