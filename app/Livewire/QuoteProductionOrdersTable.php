<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\ProductionOrder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class QuoteProductionOrdersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Quote $quote;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductionOrder::query()
                    ->where('quote_id', $this->quote->id)
                    ->orderBy('version', 'desc')
            )
            ->columns([
                TextColumn::make('version')
                    ->label('Version')
                    ->badge()
                    ->color(fn (ProductionOrder $record): string => $record->state === 'current' ? 'success' : 'gray')
                    ->weight('semibold')
                    ->formatStateUsing(fn ($state) => 'v' . $state)
                    ->description(fn (ProductionOrder $record) => $record->state === 'current' ? 'Current' : 'Previous')
                    ->sortable(),
                TextColumn::make('state')
                    ->label('State')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'current' => 'success',
                        'historic' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('archive_path')
                    ->label('Archive')
                    ->icon('heroicon-o-document')
                    ->iconColor('warning')
                    ->formatStateUsing(function ($state, $record) {
                        // Generate filename like PO-834893-v2.pdf
                        $quoteNumber = $this->quote->quote_number ?? $this->quote->id;
                        return "PO-{$quoteNumber}-v{$record->version}.pdf";
                    }),
                TextColumn::make('preview')
                    ->label('Preview')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn () => 'Open')
                    ->url('#'),
                TextColumn::make('loading_date')
                    ->label('Loading date')
                    ->icon('heroicon-o-calendar')
                    ->dateTime('M d, Y h:i a')
                    ->sortable(),
                TextColumn::make('uploader.name')
                    ->label('Uploaded by')
                    ->icon('heroicon-o-user')
                    ->default('Admin'),
            ])
            ->actions([
                Tables\Actions\Action::make('viewDiff')
                    ->label('View diff')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('gray')
                    ->url('#'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importOrder')
                    ->label('+ Import Order')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->url('#'),
            ])
            ->emptyStateHeading('No production orders yet')
            ->emptyStateDescription('Import a production order to start tracking.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->paginated([10, 25, 50]);
    }

    public function render(): View
    {
        return view('livewire.quote-production-orders-table');
    }
}
