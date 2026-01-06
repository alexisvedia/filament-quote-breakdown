<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use App\Models\Style;
use App\Models\CostItem;
use App\Models\SupplierCostItem;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StyleBreakdown extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = QuoteResource::class;

    protected static string $view = 'filament.resources.quote-resource.pages.style-breakdown';

    public Quote $quote;
    public Style $style;
    public $suppliers;
    public array $matrix = [];
    public array $supplierTotals = [];

    public function mount($record, $style): void
    {
        $this->quote = Quote::query()->findOrFail($record);
        $this->style = Style::query()
            ->whereKey($style)
            ->where('quote_id', $this->quote->getKey())
            ->firstOrFail();
        $this->suppliers = $this->style->suppliers()->orderBy('name')->get();
        $this->buildMatrix();
    }

    protected function buildMatrix(): void
    {
        $this->matrix = [];
        $this->supplierTotals = [];

        $itemIds = $this->style->costItems()->pluck('id');
        $supplierIds = $this->suppliers->pluck('id');

        $rows = SupplierCostItem::query()
            ->whereIn('cost_item_id', $itemIds)
            ->whereIn('supplier_id', $supplierIds)
            ->get(['cost_item_id', 'supplier_id', 'cost', 'margin_percent']);

        foreach ($rows as $row) {
            $this->matrix[$row->cost_item_id][$row->supplier_id] = [
                'cost' => $row->cost,
                'margin' => $row->margin_percent,
            ];
        }

        foreach ($supplierIds as $sid) {
            $sum = 0.0;
            foreach ($itemIds as $iid) {
                $sum += (float) ($this->matrix[$iid][$sid]['cost'] ?? 0);
            }
            $this->supplierTotals[$sid] = $sum;
        }
    }

    protected function getTableQuery(): Builder
    {
        return CostItem::query()->where('style_id', $this->style->getKey());
    }

    public function table(Table $table): Table
    {
        $supplierColumnGroups = $this->suppliers->map(function ($supplier) {
            $sid = $supplier->getKey();

            return ColumnGroup::make($supplier->name, [
                TextColumn::make("supplier_{$sid}_cost")
                    ->label('Cost')
                    ->alignEnd()
                    ->state(fn (CostItem $record) => $this->matrix[$record->getKey()][$sid]['cost'] ?? null)
                    ->money('usd', true),

                TextColumn::make("supplier_{$sid}_margin")
                    ->label('Margin %')
                    ->alignEnd()
                    ->state(fn (CostItem $record) => $this->matrix[$record->getKey()][$sid]['margin'] ?? null)
                    ->formatStateUsing(fn ($state) => filled($state) ? number_format((float) $state, 0) . '%' : '—'),
            ]);
        })->all();

        return $table
            ->query($this->getTableQuery())
            ->defaultSort('sort_order')
            ->paginated(false)
            ->striped()
            ->groups([
                Group::make('category')->collapsible(),
            ])
            ->columns([
                TextColumn::make('name')
                    ->label('Item')
                    ->searchable()
                    ->wrap(),

                ...$supplierColumnGroups,
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Quote')
                ->url(fn () => QuoteResource::getUrl('view', ['record' => $this->quote])),
        ];
    }

    public function getTitle(): string
    {
        $styleLabel = $this->style->code ?? $this->style->name ?? ('Style #' . $this->style->getKey());
        return $styleLabel . ' — Itemized Comparison';
    }
}