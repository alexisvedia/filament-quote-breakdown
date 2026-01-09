<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestQuotationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Latest quotations';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Quote::query()->latest())
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
                    ->label('Delivery')
                    ->date('d/m/Y')
                    ->icon('heroicon-m-clock')
                    ->color('success'),
                Tables\Columns\TextColumn::make('fob_price')
                    ->label('FOB Price')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2) . ' US$'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10]);
    }
}
