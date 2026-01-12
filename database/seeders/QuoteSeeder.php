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

        // Create quotes
        $quotes = [
            Quote::create([
                'name' => 'Summer Collection 2026',
                'style_number' => 'SC-001',
                'status' => 'pending',
                'total' => 0,
            ]),
            Quote::create([
                'name' => 'Winter Line 2026',
                'style_number' => 'WL-002',
                'status' => 'approved',
                'total' => 0,
            ]),
        ];

        // Item types for techpack breakdown
        $itemTypes = ['Fabric', 'Trim', 'Label', 'Packaging', 'CMT (Labor)', 'Shipping'];

        // Create quote items for each quote and supplier
        foreach ($quotes as $quote) {
            $quoteTotal = 0;
            foreach ($suppliers as $supplier) {
                foreach ($itemTypes as $itemName) {
                    $unitPrice = rand(100, 1000) / 100;
                    $quantity = rand(100, 500);
                    $total = $unitPrice * $quantity;
                    $quoteTotal += $total;

                    QuoteItem::create([
                        'quote_id' => $quote->id,
                        'supplier_id' => $supplier->id,
                        'item_name' => $itemName,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'total' => $total,
                    ]);
                }
            }
            $quote->update(['total' => $quoteTotal]);
        }
    }
}
