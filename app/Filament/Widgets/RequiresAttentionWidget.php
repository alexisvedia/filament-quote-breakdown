<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

/**
 * MOCK COMPONENT - Solo UI Visual
 * NO funcional - demuestra interfaz solamente.
 * Programador implementara logica real.
 */
class RequiresAttentionWidget extends Widget
{
    protected static string $view = 'filament.widgets.requires-attention-widget';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $overdueQuotes = Quote::whereNotNull('delivery_date')
            ->whereDate('delivery_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        $pendingCostsheets = Quote::where('status', 'pending')->count();

        $unresponsiveSuppliers = DB::table('quote_supplier')
            ->whereNull('responded_at')
            ->count();

        return [
            'cards' => [
                [
                    'title' => 'Overdue quotes',
                    'count' => $overdueQuotes,
                    'description' => 'Past deadline and need action',
                    'action' => 'Review quotes',
                    'url' => QuoteResource::getUrl('index'),
                    'color' => 'danger',
                ],
                [
                    'title' => 'Pending costsheets',
                    'count' => $pendingCostsheets,
                    'description' => 'Waiting for supplier pricing',
                    'action' => 'View costsheets',
                    'url' => QuoteResource::getUrl('index'),
                    'color' => 'warning',
                ],
                [
                    'title' => 'Suppliers silent',
                    'count' => $unresponsiveSuppliers,
                    'description' => 'Invited but no response yet',
                    'action' => 'Send reminders',
                    'url' => QuoteResource::getUrl('index'),
                    'color' => 'info',
                ],
            ],
        ];
    }
}
