<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class StyleBreakdown extends Page
{
    use InteractsWithRecord;

    protected static string $resource = QuoteResource::class;
    protected static string $view = 'filament.resources.quote-resource.pages.style-breakdown';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function resolveRecord(int|string $key): Quote
    {
        return Quote::with(['items.supplier'])->findOrFail($key);
    }

    public function getTitle(): string
    {
        return "Desglose: {$this->record->name}";
    }

    public function getItemsByType(): array
    {
        $itemTypes = ['Fabric', 'Trim', 'Label', 'Packaging', 'CMT (Labor)', 'Shipping'];
        $suppliers = $this->record->items->pluck('supplier')->unique('id');
        $breakdown = [];

        foreach ($itemTypes as $type) {
            $row = ['type' => $type];
            foreach ($suppliers as $supplier) {
                $item = $this->record->items
                    ->where('item_name', $type)
                    ->where('supplier_id', $supplier->id)
                    ->first();
                $row[$supplier->name] = $item ? $item->total : null;
            }
            $breakdown[] = $row;
        }

        return ['suppliers' => $suppliers, 'breakdown' => $breakdown];
    }
}
