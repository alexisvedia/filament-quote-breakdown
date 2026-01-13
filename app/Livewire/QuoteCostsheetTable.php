<?php

namespace App\Livewire;

use App\Models\CostsheetVersion;
use App\Models\Quote;
use App\Models\Supplier;
use Filament\Notifications\Notification;
use Illuminate\View\View;
use Livewire\Component;

class QuoteCostsheetTable extends Component
{
    public Quote $quote;
    public bool $showDetailModal = false;
    public bool $showHistoryModal = false;
    public bool $showDifferencesOnly = false;
    public ?string $selectedTechpackCode = null;
    public array $detailData = [];
    public ?int $selectedVersionId = null;

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

        // Generate example data with multiple techpacks and statuses (like Figma design)
        $exampleTechpacks = [
            [
                'style_code' => 'TP-002-2025',
                'prices' => [
                    1 => ['price' => 500.23, 'status' => null],
                    2 => ['price' => 546.00, 'status' => null],
                    3 => ['price' => null, 'status' => 'pending'],
                    4 => ['price' => 356.00, 'status' => null],
                ],
            ],
            [
                'style_code' => 'TP-001-2025',
                'prices' => [
                    1 => ['price' => 600.63, 'status' => null],
                    2 => ['price' => 600.63, 'status' => 'under_review'],
                    3 => ['price' => null, 'status' => 'pending'],
                    4 => ['price' => 622.00, 'status' => null],
                ],
            ],
        ];

        $exampleSuppliers = [
            1 => 'Damir Trading SAC',
            2 => 'Diseño ACMM SAC',
            3 => 'Marga SRL',
            4 => 'Ideas y Soluciones',
        ];

        return [
            'techpack' => [
                'id' => $techpack?->id,
                'style_code' => $styleCode,
            ],
            'suppliers' => !empty($suppliers) ? $suppliers : $exampleSuppliers,
            'totals' => $supplierTotals,
            'techpacks' => $exampleTechpacks,
            'exampleSuppliers' => $exampleSuppliers,
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

    public function toggleDifferencesOnly(): void
    {
        $this->showDifferencesOnly = !$this->showDifferencesOnly;
    }

    public function getHistoryData(): array
    {
        $realData = CostsheetVersion::where('quote_id', $this->quote->id)
            ->with(['supplier', 'user'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($version) {
                return [
                    'id' => $version->id,
                    'version_number' => $version->version_number,
                    'supplier' => $version->supplier?->company ?? 'Unknown',
                    'total_cost' => $version->total_cost,
                    'user' => $version->user?->name ?? 'System',
                    'notes' => $version->notes,
                    'created_at' => $version->created_at,
                ];
            })
            ->toArray();

        // If no real data, return example data
        if (empty($realData)) {
            return [
                [
                    'id' => 1,
                    'version_number' => 3,
                    'supplier' => 'Damir Trading SAC',
                    'total_cost' => 500.23,
                    'user' => 'Maria Garcia',
                    'notes' => 'Updated fabric costs after negotiation',
                    'created_at' => now()->subDays(1),
                ],
                [
                    'id' => 2,
                    'version_number' => 2,
                    'supplier' => 'Diseño ACMM SAC',
                    'total_cost' => 546.00,
                    'user' => 'Carlos Lopez',
                    'notes' => 'Added CMT labor costs',
                    'created_at' => now()->subDays(3),
                ],
                [
                    'id' => 3,
                    'version_number' => 1,
                    'supplier' => 'Ideas y Soluciones',
                    'total_cost' => 622.00,
                    'user' => 'Admin',
                    'notes' => 'Initial costsheet version',
                    'created_at' => now()->subDays(7),
                ],
            ];
        }

        return $realData;
    }

    public function saveVersion(?int $supplierId = null, ?string $notes = null): void
    {
        $items = $this->quote->items()->with('supplier')->get();

        // If no supplier specified, save for all suppliers
        $supplierIds = $supplierId
            ? [$supplierId]
            : $items->pluck('supplier_id')->unique()->filter()->toArray();

        foreach ($supplierIds as $sid) {
            $supplierItems = $items->where('supplier_id', $sid);
            $totalCost = $supplierItems->sum('total');

            // Get next version number for this quote+supplier
            $lastVersion = CostsheetVersion::where('quote_id', $this->quote->id)
                ->where('supplier_id', $sid)
                ->max('version_number') ?? 0;

            CostsheetVersion::create([
                'quote_id' => $this->quote->id,
                'supplier_id' => $sid,
                'user_id' => auth()->id(),
                'version_number' => $lastVersion + 1,
                'items_snapshot' => $supplierItems->map(fn($item) => [
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ])->toArray(),
                'total_cost' => $totalCost,
                'notes' => $notes,
            ]);
        }

        Notification::make()
            ->title('Version saved')
            ->body('Costsheet version has been saved to history.')
            ->success()
            ->send();
    }

    public function openHistoryModal(): void
    {
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->selectedVersionId = null;
    }

    public function viewVersion(int $versionId): array
    {
        $version = CostsheetVersion::with(['supplier', 'user'])->find($versionId);

        if (!$version) {
            return [];
        }

        return [
            'version_number' => $version->version_number,
            'supplier' => $version->supplier?->company ?? 'Unknown',
            'total_cost' => $version->total_cost,
            'user' => $version->user?->name ?? 'System',
            'notes' => $version->notes,
            'created_at' => $version->created_at->format('M d, Y H:i'),
            'items' => $version->items_snapshot ?? [],
        ];
    }

    public function render(): View
    {
        return view('livewire.quote-costsheet-table', [
            'costsheetData' => $this->getCostsheetData(),
            'historyData' => $this->getHistoryData(),
        ]);
    }
}
