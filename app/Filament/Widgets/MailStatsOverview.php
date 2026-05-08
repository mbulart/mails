<?php

namespace App\Filament\Widgets;

use App\Enums\MailLogStatus;
use App\Models\ApiConsumer;
use App\Models\MailLog;
use App\Models\MailType;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MailStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Emails envoyés', MailLog::query()->where('status', MailLogStatus::Sent)->count())
                ->description('Total marqués comme envoyés')
                ->icon('heroicon-o-paper-airplane'),
            Stat::make('Emails échoués', MailLog::query()->where('status', MailLogStatus::Failed)->count())
                ->description('À surveiller')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
            Stat::make('Types actifs', MailType::query()->where('is_active', true)->count())
                ->icon('heroicon-o-envelope'),
            Stat::make('Consommateurs actifs', ApiConsumer::query()->where('is_active', true)->count())
                ->icon('heroicon-o-key'),
        ];
    }
}
