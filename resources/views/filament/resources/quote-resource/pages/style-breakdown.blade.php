<x-filament-panels::page>
    @php $data = $this->getItemsByType(); $suppliers = $data['suppliers']; $breakdown = $data['breakdown']; @endphp
    <div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="p-4"><h3 class="text-lg font-semibold">{{ $this->record->name }} - {{ $this->record->style_number }}</h3>
        <span class="text-sm text-gray-500">Total: ${{ number_format($this->record->total, 2) }}</span></div>
        <div class="overflow-x-auto">
        <table class="w-full table-auto divide-y divide-gray-200 dark:divide-white/5">
        <thead class="bg-gray-50 dark:bg-white/5"><tr>
        <th class="px-4 py-3 text-left text-sm font-semibold">Item</th>
        @foreach($suppliers as $supplier)<th class="px-4 py-3 text-right text-sm font-semibold">{{ $supplier->name }}</th>@endforeach
        </tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
        @foreach($breakdown as $row)<tr class="hover:bg-gray-50 dark:hover:bg-white/5">
        <td class="px-4 py-3 text-sm font-medium">{{ $row['type'] }}</td>
        @foreach($suppliers as $supplier)<td class="px-4 py-3 text-right text-sm">
        @if(isset($row[$supplier->name]))${{ number_format($row[$supplier->name], 2) }}@else - @endif
        </td>@endforeach
        </tr>@endforeach
        </tbody>
        <tfoot class="bg-gray-50 dark:bg-white/5"><tr>
        <td class="px-4 py-3 text-sm font-bold">TOTAL</td>
        @foreach($suppliers as $supplier)
        @php $t = collect($breakdown)->sum(fn($r) => $r[$supplier->name] ?? 0); @endphp
        <td class="px-4 py-3 text-right text-sm font-bold text-primary-600">${{ number_format($t, 2) }}</td>
        @endforeach
        </tr></tfoot>
        </table></div></div>
        </x-filament-panels::page>