<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Quotation')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $overdueQuery = Quote::query()
            ->whereNotNull('delivery_date')
            ->whereDate('delivery_date', '<', Carbon::today())
            ->where('status', '!=', 'completed');

        return [
            'all' => Tab::make('All')
                ->badge(Quote::count())
                ->badgeColor('gray'),
            'pending' => Tab::make('Pending')
                ->badge(Quote::where('status', 'pending')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'in_production' => Tab::make('In Production')
                ->badge(Quote::where('status', 'in_production')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_production')),
            'completed' => Tab::make('Completed')
                ->badge(Quote::where('status', 'completed')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
            'overdue' => Tab::make('Overdue')
                ->badge($overdueQuery->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereNotNull('delivery_date')
                    ->whereDate('delivery_date', '<', Carbon::today())
                    ->where('status', '!=', 'completed')),
        ];
    }
}
