<?php

namespace App\Livewire;

use App\Models\SampleOrder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;

class QuoteSampleOrdersTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $quoteId;

    public function mount($quoteId)
    {
        $this->quoteId = $quoteId;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SampleOrder::query()->where('quote_id', $this->quoteId)
            )
            ->columns([
                TextColumn::make('tp_code')
                    ->label('TP Code')
                    ->color('warning')
                    ->weight('medium')
                    ->formatStateUsing(fn ($state) => '# ' . $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('techpack.style_name')
                    ->label('Techpack')
                    ->weight('semibold')
                    ->searchable(),
                TextColumn::make('requested_by')
                    ->label('Requested by')
                    ->icon('heroicon-o-user')
                    ->searchable(),
                TextColumn::make('request_date')
                    ->label('Date')
                    ->icon('heroicon-o-calendar')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('eta')
                    ->label('ETA')
                    ->icon('heroicon-o-clock')
                    ->iconColor('warning')
                    ->color(function ($state): string {
                        if (!$state) {
                            return 'gray';
                        }

                        $date = $state instanceof Carbon ? $state : Carbon::parse($state);
                        if ($date->isPast()) {
                            return 'danger';
                        }

                        return $date->diffInDays(now()) <= 3 ? 'warning' : 'success';
                    })
                    ->date('M d, Y')
                    ->description(function ($state): string {
                        if (!$state) {
                            return 'No ETA';
                        }

                        $date = $state instanceof Carbon ? $state : Carbon::parse($state);
                        $diff = Carbon::today()->diffInDays($date->copy()->startOfDay(), false);

                        if ($diff < 0) {
                            return abs($diff) . 'd overdue';
                        }

                        if ($diff === 0) {
                            return 'Due today';
                        }

                        return $diff . 'd left';
                    })
                    ->sortable(),
                TextColumn::make('supplier.company')
                    ->label('Supplier')
                    ->icon('heroicon-o-building-office')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('State')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending', 'in_progress' => 'Sent to',
                        'received' => 'Received',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending', 'in_progress' => 'warning',
                        'received' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewSizes')
                    ->label('Detail')
                    ->icon('heroicon-o-table-cells')
                    ->modalHeading(fn ($record) => 'Detail by Size - ' . $record->techpack?->style_name)
                    ->modalDescription('Quantities ordered by the customer, for WTS and received')
                    ->modalContent(fn ($record) => view('filament.modals.sample-order-sizes', ['sizes' => $record->sizes]))
                    ->modalSubmitAction(false),
            ])
            ->headerActions([
                Tables\Actions\Action::make('requestSampleOrder')
                    ->label('+ Request Sample Order')
                    ->color('warning')
                    ->icon('heroicon-o-plus')
                    ->url('#'),
            ])
            ->emptyStateHeading('No sample orders yet')
            ->emptyStateDescription('Request a sample order to start tracking samples.')
            ->emptyStateIcon('heroicon-o-clipboard-document')
            ->paginated([10, 25, 50]);
    }

    public function render(): View
    {
        return view('livewire.quote-sample-orders-table');
    }
}
