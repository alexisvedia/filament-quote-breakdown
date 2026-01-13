<?php

namespace Database\Seeders;

use App\Models\ActionPlanItem;
use App\Models\ProductionOrder;
use App\Models\Quote;
use App\Models\QuoteMessage;
use App\Models\SampleOrder;
use App\Models\Supplier;
use App\Models\Techpack;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class QuoteMockDataSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = Quote::all();
        $suppliers = Supplier::all();
        $techpacks = Techpack::all();
        $user = User::first();

        if ($quotes->isEmpty() || $suppliers->isEmpty() || !$user) {
            return;
        }

        $landmarks = ['yarn', 'fabric', 'cut', 'sewing', 'washing', 'finishing', 'packaging', 'shipping'];

        foreach ($quotes as $index => $quote) {
            $quoteSuppliers = $suppliers->slice($index, 3)->values();
            if ($quoteSuppliers->isEmpty()) {
                $quoteSuppliers = $suppliers->take(3);
            }

            foreach ($quoteSuppliers as $sIndex => $supplier) {
                $status = ['pending', 'accepted', 'rejected'][$sIndex % 3];
                $invitedAt = now()->subDays(7 + $sIndex);
                $respondedAt = $status === 'pending' ? null : $invitedAt->copy()->addDays(1 + $sIndex);
                $deadline = now()->addDays(3 - $sIndex);

                $quote->suppliers()->syncWithoutDetaching([
                    $supplier->id => [
                        'status' => $status,
                        'invited_at' => $invitedAt,
                        'responded_at' => $respondedAt,
                        'deadline' => $deadline,
                        'invitation_message' => 'Please submit your costsheet by the deadline.',
                    ],
                ]);
            }

            $techpackId = $quote->techpack_id ?? $techpacks->first()?->id;
            if ($techpackId) {
                foreach ($quoteSuppliers as $sIndex => $supplier) {
                    SampleOrder::updateOrCreate(
                        [
                            'quote_id' => $quote->id,
                            'supplier_id' => $supplier->id,
                            'tp_code' => 'TP-' . str_pad((string) $quote->id, 3, '0', STR_PAD_LEFT),
                        ],
                        [
                            'techpack_id' => $techpackId,
                            'requested_by' => 'Maria Garcia',
                            'request_date' => now()->subDays(10 + $sIndex),
                            'eta' => now()->addDays(5 - $sIndex),
                            'status' => $sIndex % 2 === 0 ? 'in_progress' : 'pending',
                            'sizes' => [
                                ['size' => 'S', 'qty' => 8],
                                ['size' => 'M', 'qty' => 12],
                                ['size' => 'L', 'qty' => 10],
                            ],
                        ]
                    );
                }
            }

            $currentVersion = 2;
            foreach ([1, 2] as $version) {
                ProductionOrder::updateOrCreate(
                    [
                        'quote_id' => $quote->id,
                        'version' => (string) $version,
                    ],
                    [
                        'state' => $version === $currentVersion ? 'current' : 'historic',
                        'archive_path' => null,
                        'archive_name' => "PO-{$quote->id}-v{$version}.pdf",
                        'loading_date' => now()->addDays(20 + $version),
                        'uploaded_by' => $user->id,
                    ]
                );
            }

            $startDate = Carbon::now()->addDays(5);
            foreach ($landmarks as $lIndex => $landmark) {
                $planDate = $startDate->copy()->addDays($lIndex * 3);
                $realDate = $lIndex < 3 ? $planDate->copy()->addDays($lIndex % 2) : null;
                $state = $realDate
                    ? ($realDate->greaterThan($planDate) ? 'delayed' : 'completed')
                    : 'pending';

                ActionPlanItem::updateOrCreate(
                    [
                        'quote_id' => $quote->id,
                        'landmark' => $landmark,
                    ],
                    [
                        'plan_date' => $planDate,
                        'real_date' => $realDate,
                        'state' => $state,
                    ]
                );
            }

            $supplierForMessages = $quoteSuppliers->first();
            if ($supplierForMessages) {
                QuoteMessage::updateOrCreate(
                    [
                        'quote_id' => $quote->id,
                        'supplier_id' => $supplierForMessages->id,
                        'body' => 'Can you confirm lead times for the techpack?',
                    ],
                    [
                        'user_id' => $user->id,
                        'is_from_wts' => true,
                        'is_read' => true,
                    ]
                );

                QuoteMessage::updateOrCreate(
                    [
                        'quote_id' => $quote->id,
                        'supplier_id' => $supplierForMessages->id,
                        'body' => 'Lead time is 45 days from PO confirmation.',
                    ],
                    [
                        'user_id' => $user->id,
                        'is_from_wts' => false,
                        'is_read' => false,
                    ]
                );
            }
        }
    }
}
