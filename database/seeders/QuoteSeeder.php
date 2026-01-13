<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Supplier;
use App\Models\Techpack;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        // Use existing suppliers from SupplierSeeder
        $suppliers = Supplier::take(3)->get();
        $clientIds = Client::pluck('id')->values();
        $techpackIds = Techpack::pluck('id')->values();

        // Define quotes data
        $quotesData = [
            [
                'name' => 'RFQ-1024',
                'style_number' => 'SC-001',
                'status' => 'pending',
                'client_id' => $clientIds->get(0),
                'techpack_id' => $techpackIds->get(0),
                'season' => 'SS 2026',
                'date' => now()->subDays(12),
                'delivery_date' => now()->addDays(4),
                'fob_price' => 14.50,
            ],
            [
                'name' => 'RFQ-1025',
                'style_number' => 'WL-002',
                'status' => 'in_production',
                'client_id' => $clientIds->get(1) ?? $clientIds->get(0),
                'techpack_id' => $techpackIds->get(1) ?? $techpackIds->get(0),
                'season' => 'AW 2026',
                'date' => now()->subDays(20),
                'delivery_date' => now()->addDays(12),
                'fob_price' => 18.90,
            ],
            [
                'name' => 'RFQ-1026',
                'style_number' => 'SP-003',
                'status' => 'completed',
                'client_id' => $clientIds->get(0),
                'techpack_id' => $techpackIds->get(2) ?? $techpackIds->get(0),
                'season' => 'SS 2025',
                'date' => now()->subDays(45),
                'delivery_date' => now()->subDays(10),
                'fob_price' => 12.75,
            ],
            [
                'name' => 'RFQ-1027',
                'style_number' => 'SR-004',
                'status' => 'pending',
                'client_id' => $clientIds->get(2) ?? $clientIds->get(0),
                'techpack_id' => $techpackIds->get(0),
                'season' => 'AW 2025',
                'date' => now()->subDays(18),
                'delivery_date' => now()->subDays(2),
                'fob_price' => 16.20,
            ],
        ];

        // Item types for techpack breakdown
        $itemTypes = ['Fabric', 'Trim', 'Label', 'Packaging', 'CMT (Labor)', 'Shipping'];

        // Create or update quotes
        foreach ($quotesData as $quoteData) {
            $quote = Quote::updateOrCreate(
                ['style_number' => $quoteData['style_number']],
                array_merge($quoteData, ['total' => 0])
            );

            $quoteTotal = 0;

            // Create quote items for each supplier
            foreach ($suppliers as $supplier) {
                foreach ($itemTypes as $itemName) {
                    // Use deterministic values based on quote and supplier for consistency
                    $seed = crc32($quote->style_number . $supplier->id . $itemName);
                    $unitPrice = ($seed % 900 + 100) / 100;
                    $quantity = ($seed % 400) + 100;
                    $total = $unitPrice * $quantity;
                    $quoteTotal += $total;

                    QuoteItem::updateOrCreate(
                        [
                            'quote_id' => $quote->id,
                            'supplier_id' => $supplier->id,
                            'item_name' => $itemName,
                        ],
                        [
                            'unit_price' => $unitPrice,
                            'quantity' => $quantity,
                            'total' => $total,
                        ]
                    );
                }
            }

            $quote->update(['total' => $quoteTotal]);
        }
    }
}
