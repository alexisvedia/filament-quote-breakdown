<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\Techpack;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class QuoteStylesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Quote $quote;
    public string $heading = 'Styles';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Techpack::query()->where('id', $this->quote->techpack_id)
            )
            ->columns([
                TextColumn::make('style_code')
                    ->label('Code')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('style_name')
                    ->label('Name')
                    ->weight('semibold')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('season')
                    ->label('Season')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('status')
                    ->label('State')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'under_review' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
            ])
            ->actions([
                Action::make('view_detail')
                    ->label('View Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Techpack $record): string => route('filament.admin.resources.techpacks.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }

    public function render(): View
    {
        return view('livewire.quote-styles-table');
    }
}
