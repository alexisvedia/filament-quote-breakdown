<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\ActionPlanItem;
use App\Models\ProductionOrder;
use App\Models\Quote;
use App\Models\SampleOrder;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Carbon;
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
                                        'approved' => 'success',
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

    public function getTabCounts(): array
    {
        $quoteId = $this->record->id;

        return [
            'styles' => $this->record->techpack_id ? 1 : 0,
            'costsheet' => $this->record->items()->count(),
            'samples' => SampleOrder::where('quote_id', $quoteId)->count(),
            'production' => ProductionOrder::where('quote_id', $quoteId)->count(),
            'action_plan' => ActionPlanItem::where('quote_id', $quoteId)->count(),
            'suppliers' => $this->record->suppliers()->count(),
            'comments' => $this->record->comments()->count(),
            'messages' => $this->record->messages()->count(),
        ];
    }

    public function getSupplierProgress(): array
    {
        $total = $this->record->suppliers()->count();
        $responded = $this->record->suppliers()
            ->whereNotNull('quote_supplier.responded_at')
            ->count();

        $percent = $total > 0 ? (int) round(($responded / $total) * 100) : 0;

        return [
            'total' => $total,
            'responded' => $responded,
            'percent' => $percent,
        ];
    }

    public function getDeadlineMeta(): array
    {
        $deadline = $this->record->deadline ?? $this->record->delivery_date;
        if (!$deadline) {
            return [
                'date' => 'Not set',
                'label' => 'Set a deadline to track urgency',
                'status' => 'none',
            ];
        }

        $date = $deadline instanceof Carbon ? $deadline : Carbon::parse($deadline);
        $diff = Carbon::today()->diffInDays($date->copy()->startOfDay(), false);

        if ($diff < 0) {
            return [
                'date' => $date->format('M d, Y'),
                'label' => abs($diff) . 'd overdue',
                'status' => 'overdue',
            ];
        }

        if ($diff === 0) {
            return [
                'date' => $date->format('M d, Y'),
                'label' => 'Due today',
                'status' => 'today',
            ];
        }

        $status = $diff <= 3 ? 'soon' : 'on_track';

        return [
            'date' => $date->format('M d, Y'),
            'label' => $diff . 'd left',
            'status' => $status,
        ];
    }
}
