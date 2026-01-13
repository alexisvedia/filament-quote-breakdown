<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $activeQuotes = Quote::whereIn('status', ['pending', 'in_production', 'approved'])->count();
        $overdueQuotes = Quote::whereNotNull('delivery_date')
            ->whereDate('delivery_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();
        $totalValue = Quote::sum('fob_price') ?? 0;
        $completedThisMonth = Quote::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            Stat::make('Active Quotes', $activeQuotes)
                ->description('Pending and in production')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('success'),
            Stat::make('Overdue Deadlines', $overdueQuotes)
                ->description('Quotes past deadline')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueQuotes > 0 ? 'danger' : 'success'),
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
