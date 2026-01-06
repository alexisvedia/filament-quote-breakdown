<x-filament::page>
    <x-filament::section heading="Context">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <div class="text-xs text-gray-500">Quote</div>
                <div class="text-sm font-medium">
                    {{ $this->quote->quotation_no ?? ('#' . $this->quote->getKey()) }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-500">Buyer</div>
                <div class="text-sm font-medium">
                    {{ $this->quote->buyer_name ?? '—' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-500">Style / Techpack</div>
                <div class="text-sm font-medium">
                    {{ $this->style->code ?? $this->style->name ?? ('Style #' . $this->style->getKey()) }}
                </div>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section heading="Itemized breakdown" description="Comparación item por item por proveedor">
        {{ $this->table }}
    </x-filament::section>

    <x-filament::section heading="Totals" description="Suma de ítems por proveedor (debería coincidir con tu tabla anterior)">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            @foreach ($this->suppliers as $supplier)
                <div class="rounded-xl border border-gray-200 p-4">
                    <div class="text-sm font-medium">{{ $supplier->name }}</div>
                    <div class="mt-2 text-sm tabular-nums">
                        {{ number_format($this->supplierTotals[$supplier->id] ?? 0, 2) }}
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament::page>