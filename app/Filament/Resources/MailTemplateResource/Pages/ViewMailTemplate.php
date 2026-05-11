<?php

namespace App\Filament\Resources\MailTemplateResource\Pages;

use App\Filament\Resources\MailTemplateResource;
use App\Models\MailTemplate;
use App\Services\TemplateRenderer;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewMailTemplate extends ViewRecord
{
    protected static string $resource = MailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(
            [
                Action::make('previewHtml')
                    ->label('Previsualiser HTML')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Previsualisation HTML')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->modalContent(function (): \Illuminate\Contracts\View\View {
                        /** @var MailTemplate $record */
                        $record = $this->record;
                        $samples = MailTemplate::previewSampleVariables($record->variables);
                        $html = app(TemplateRenderer::class)->render($record->html_content, $samples);

                        return view('filament.mail-template-html-preview', ['html' => $html]);
                    }),
            ],
            parent::getHeaderActions(),
        );
    }
}
