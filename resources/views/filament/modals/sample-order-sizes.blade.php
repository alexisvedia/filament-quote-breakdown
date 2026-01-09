<div class="p-4">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b">
                <th class="text-left py-2 px-3 font-medium">Size</th>
                <th class="text-center py-2 px-3 font-medium">Customer Qty</th>
                <th class="text-center py-2 px-3 font-medium">WTS Qty</th>
                <th class="text-center py-2 px-3 font-medium">Received</th>
                <th class="text-center py-2 px-3 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sizes ?? [] as $size => $data)
                <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="py-2 px-3 font-medium">{{ $size }}</td>
                    <td class="py-2 px-3 text-center">{{ $data['customer_qty'] ?? 0 }}</td>
                    <td class="py-2 px-3 text-center">{{ $data['wts_qty'] ?? 0 }}</td>
                    <td class="py-2 px-3 text-center">{{ $data['received_qty'] ?? 0 }}</td>
                    <td class="py-2 px-3 text-center">
                        @php
                            $received = $data['received_qty'] ?? 0;
                            $wtsQty = $data['wts_qty'] ?? 0;
                            $status = $received >= $wtsQty ? 'Complete' : ($received > 0 ? 'Partial' : 'Pending');
                            $statusClass = $received >= $wtsQty ? 'bg-green-100 text-green-800' : ($received > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800');
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">No size data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
