<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $activeQuotes = Quote::where('status', 'active')->count();
        $totalValue = Quote::sum('fob_price') ?? 0;
        $completedThisMonth = Quote::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            Stat::make('Active Quotes', $activeQuotes)
                ->description('0 overdue')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('success'),
            Stat::make('Performance SLA', '0%')
                ->description('Compliance this month')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
            Stat::make('Total Asset Value', '$' . number_format($totalValue, 0))
                ->description('In open quotations')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),
            Stat::make('Completed this Month', $completedThisMonth)
                ->description('Same as last month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
