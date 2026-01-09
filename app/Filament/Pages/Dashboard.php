<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestQuotationsWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Home';
    protected static ?string $title = 'Home';
    protected static ?int $navigationSort = -2;

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            LatestQuotationsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}
