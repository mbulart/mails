<?php

namespace App\Filament\Resources\MailTemplateResource\Pages;

use App\Filament\Resources\MailTemplateResource;
use App\Models\MailTemplate;
use App\Services\TemplateRenderer;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditMailTemplate extends EditRecord
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
                        $state = $this->form->getState();
                        $htmlContent = $state['html_content'] ?? $record->html_content;
                        $variables = $state['variables'] ?? $record->variables;
                        $samples = MailTemplate::previewSampleVariables(is_array($variables) ? $variables : []);
                        $html = app(TemplateRenderer::class)->render((string) $htmlContent, $samples);

                        return view('filament.mail-template-html-preview', ['html' => $html]);
                    }),
            ],
            parent::getHeaderActions(),
        );
    }
}
