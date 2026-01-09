<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\Supplier;
use Illuminate\View\View;
use Livewire\Component;

class QuoteCostsheetTable extends Component
{
    public Quote $quote;
    public bool $showDetailModal = false;
    public ?string $selectedTechpackCode = null;
    public array $detailData = [];

    public function getCostsheetData(): array
    {
        $items = $this->quote->items()->with('supplier')->get();
        
        // Group items by supplier and calculate totals
        $supplierTotals = [];
        $suppliers = [];
        
        foreach ($items as $item) {
            $supplierId = $item->supplier_id;
            $supplierName = $item->supplier?->company ?? 'Unknown';
            
            if (!isset($supplierTotals[$supplierId])) {
                $supplierTotals[$supplierId] = 0;
                $suppliers[$supplierId] = $supplierName;
            }
            $supplierTotals[$supplierId] += $item->total;
        }
        
        // Get techpack info
        $techpack = $this->quote->techpack;
        $styleCode = $techpack?->style_code ?? 'N/A';
        
        return [
            'techpack' => [
                'id' => $techpack?->id,
                'style_code' => $styleCode,
            ],
            'suppliers' => $suppliers,
            'totals' => $supplierTotals,
        ];
    }

    public function getDetailData(): array
    {
        $items = $this->quote->items()->with('supplier')->get();
        
        // Group by item_name (extract base item type)
        $itemTypes = ['Fabric', 'Trim', 'Label', 'Packaging', 'CMT (Labor)', 'Shipping'];
        $suppliers = [];
        $data = [];
        
        foreach ($items as $item) {
            $supplierId = $item->supplier_id;
            $supplierName = $item->supplier?->company ?? 'Unknown';
            $suppliers[$supplierId] = $supplierName;
            
            // Extract item type from item_name (e.g., "Fabric - Cotton Jersey" -> "Fabric")
            $itemType = $this->extractItemType($item->item_name, $itemTypes);
            
            if (!isset($data[$itemType])) {
                $data[$itemType] = [];
            }
            if (!isset($data[$itemType][$supplierId])) {
                $data[$itemType][$supplierId] = 0;
            }
            $data[$itemType][$supplierId] += $item->total;
        }
        
        // Calculate totals per supplier
        $totals = [];
        foreach ($suppliers as $supplierId => $name) {
            $totals[$supplierId] = 0;
            foreach ($data as $itemType => $supplierData) {
                $totals[$supplierId] += $supplierData[$supplierId] ?? 0;
            }
        }
        
        return [
            'itemTypes' => array_keys($data),
            'suppliers' => $suppliers,
            'data' => $data,
            'totals' => $totals,
        ];
    }

    private function extractItemType(string $itemName, array $knownTypes): string
    {
        foreach ($knownTypes as $type) {
            if (str_starts_with(strtolower($itemName), strtolower($type))) {
                return $type;
            }
        }
        // If no match, use the part before " - " or the whole name
        $parts = explode(' - ', $itemName);
        return $parts[0];
    }

    public function openDetailModal(): void
    {
        $this->detailData = $this->getDetailData();
        $this->selectedTechpackCode = $this->quote->techpack?->style_code ?? 'N/A';
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
    }

    public function render(): View
    {
        return view('livewire.quote-costsheet-table', [
            'costsheetData' => $this->getCostsheetData(),
        ]);
    }
}
