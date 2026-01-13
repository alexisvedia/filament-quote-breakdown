<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\QuoteResource;
use App\Models\QuoteMessage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MessagesWidget extends BaseWidget
{
    protected static ?string $heading = 'Messages';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(QuoteMessage::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('quote.name')
                    ->label('Quotation No.')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '-')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->getStateUsing(fn (QuoteMessage $record) => $record->user?->name ?? $record->supplier?->company ?? 'Unknown')
                    ->searchable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('Message')
                    ->limit(60),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Last message')
                    ->date('d/m/Y'),
            ])
            ->actions([
                Tables\Actions\Action::make('see')
                    ->label('View Details')
                    ->icon('heroicon-m-eye')
                    ->color('warning')
                    ->url(fn (QuoteMessage $record) => $record->quote ? QuoteResource::getUrl('view', ['record' => $record->quote]) : null)
                    ->disabled(fn (QuoteMessage $record) => $record->quote === null),
            ])
            ->paginated([5, 10]);
    }
}
