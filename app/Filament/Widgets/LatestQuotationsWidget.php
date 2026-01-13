<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class LatestQuotationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Latest quotations';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Quote::query()->orderByRaw(
                    "CASE 
                        WHEN delivery_date IS NULL THEN 2
                        WHEN delivery_date < ? THEN 0
                        ELSE 1
                    END, delivery_date ASC",
                    [now()->toDateString()]
                )
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('NO. RFQ')
                    ->formatStateUsing(fn ($state) => '#' . $state)
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('client.company')
                    ->label('Buyer')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-user'),
                Tables\Columns\TextColumn::make('season')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-calendar'),
                Tables\Columns\TextColumn::make('date')
                    ->date('d/m/Y')
                    ->icon('heroicon-m-calendar'),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Deadline')
                    ->date('M d, Y')
                    ->icon('heroicon-m-clock')
                    ->description(function (Quote $record): string {
                        $deadline = $record->delivery_date;
                        if (!$deadline) {
                            return 'No deadline';
                        }

                        $date = $deadline instanceof Carbon ? $deadline : Carbon::parse($deadline);
                        $diff = Carbon::today()->diffInDays($date->copy()->startOfDay(), false);

                        if ($diff < 0) {
                            return abs($diff) . 'd overdue';
                        }

                        if ($diff === 0) {
                            return 'Due today';
                        }

                        return $diff . 'd left';
                    })
                    ->color(function ($state): string {
                        if (!$state) {
                            return 'gray';
                        }

                        $date = $state instanceof Carbon ? $state : Carbon::parse($state);
                        if ($date->isPast()) {
                            return 'danger';
                        }

                        return $date->diffInDays(Carbon::today()) <= 3 ? 'warning' : 'success';
                    }),
                Tables\Columns\TextColumn::make('fob_price')
                    ->label('FOB Unit')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2) . ' US$'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10]);
    }
}
