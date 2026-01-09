<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Attributes\Url;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    protected static string $view = 'filament.resources.quote-resource.pages.view-quote';

    #[Url]
    public string $activeTab = 'styles';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('warning')
                ->label('Edit'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(7)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Quotation No.')
                                    ->color('warning')
                                    ->weight('bold'),
                                TextEntry::make('client.company')
                                    ->label('Buyer')
                                    ->icon('heroicon-o-user')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('buyer_department')
                                    ->label('Department')
                                    ->icon('heroicon-o-building-office')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('season')
                                    ->label('Season')
                                    ->icon('heroicon-o-calendar')
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('supplier.company')
                                    ->label('Supplier')
                                    ->icon('heroicon-o-building-storefront'),
                                TextEntry::make('date')
                                    ->label('Date')
                                    ->icon('heroicon-o-calendar')
                                    ->date('d/m/Y'),
                                TextEntry::make('status')
                                    ->label('State')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'completed' => 'info',
                                        'in_production' => 'warning',
                                        default => 'gray',
                                    }),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }
}
