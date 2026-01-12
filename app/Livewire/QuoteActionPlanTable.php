<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\ActionPlanItem;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QuoteActionPlanTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Quote $quote;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ActionPlanItem::query()
                    ->where('quote_id', $this->quote->id)
                    ->orderByRaw("CASE landmark
                        WHEN 'yarn' THEN 1
                        WHEN 'fabric' THEN 2
                        WHEN 'cut' THEN 3
                        WHEN 'sewing' THEN 4
                        WHEN 'washing' THEN 5
                        WHEN 'finishing' THEN 6
                        WHEN 'packaging' THEN 7
                        WHEN 'shipping' THEN 8
                        ELSE 9 END")
            )
            ->columns([
                TextColumn::make('landmark')
                    ->label('Landmark')
                    ->weight('semibold')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('plan_date')
                    ->label('Plan (TNA)')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('real_date')
                    ->label('Real (WIP)')
                    ->date('M d, Y')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('delta_days')
                    ->label('Δ days')
                    ->alignCenter()
                    ->color(fn ($state): ?string => $state !== null && $state < 0 ? 'danger' : null)
                    ->formatStateUsing(fn ($state): string => $state !== null ? (string) $state : ''),
                TextColumn::make('state')
                    ->label('State')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'delayed' => 'warning',
                        'pending' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('importTNA')
                    ->label('+ Import TNA')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url('#'),
            ])
            ->paginated([10, 25, 50]);
    }

    public function render(): View
    {
        return view('livewire.quote-action-plan-table');
    }
}
