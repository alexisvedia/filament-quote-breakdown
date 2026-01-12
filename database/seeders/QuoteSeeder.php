<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        // Use existing suppliers from SupplierSeeder
        $suppliers = Supplier::take(3)->get();

        // Define quotes data
        $quotesData = [
            [
                'name' => 'Summer Collection 2026',
                'style_number' => 'SC-001',
                'status' => 'pending',
            ],
            [
                'name' => 'Winter Line 2026',
                'style_number' => 'WL-002',
                'status' => 'approved',
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
