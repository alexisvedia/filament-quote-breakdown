<?php

namespace App\Livewire;

use App\Models\Quote;
use App\Models\ProductionOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;

class QuoteProductionOrdersTable extends Component
{
    use WithPagination;

    public Quote $quote;

    public function getProductionOrdersProperty()
    {
        return ProductionOrder::where('quote_id', $this->quote->id)
            ->with('uploader')
            ->orderBy('version', 'desc')
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.quote-production-orders-table', [
            'productionOrders' => $this->productionOrders,
        ]);
    }
}
git status