<div>
    <div class="fi-ta">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Costsheet</h3>
            </div>
            <div class="flex items-center gap-2">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="search"
                        placeholder="Search"
                        class="w-64"
                    />
                </x-filament::input.wrapper>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900">
            <table class="w-full divide-y divide-gray-200 dark:divide-white/10">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                            Style
                        </th>
                        @foreach($costsheetData['suppliers'] as $supplierId => $supplierName)
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $supplierName }}
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="#" class="text-primary-600 hover:text-primary-500 font-medium flex items-center gap-1">
                                <x-heroicon-m-hashtag class="w-4 h-4" />
                                {{ $costsheetData['techpack']['style_code'] }}
                            </a>
                        </td>
                        @foreach($costsheetData['suppliers'] as $supplierId => $supplierName)
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                @if(isset($costsheetData['totals'][$supplierId]))
                                    ${{ number_format($costsheetData['totals'][$supplierId], 2) }}
                                @else
                                    <x-filament::badge color="warning" size="sm">
                                        Pending
                                    </x-filament::badge>
                                @endif
                            </td>
                        @endforeach
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <button 
                                wire:click="openDetailModal"
                                type="button"
                                class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                            >
                                <x-heroicon-o-eye class="w-4 h-4" />
                                View Detail
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination info -->
        <div class="flex items-center justify-between mt-4 text-sm text-gray-500 dark:text-gray-400">
            <span>1 to 1 of 1 results are shown</span>
            <div class="flex items-center gap-2">
                <span>per page</span>
                <select class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDetailModal"></div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Quotations > {{ $selectedTechpackCode }}
                            </p>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ $selectedTechpackCode }}
                            </h2>
                        </div>
                        <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="px-6 py-4">
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
                        <table class="w-full divide-y divide-gray-200 dark:divide-white/10">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800">
                                        {{ $selectedTechpackCode }}
                                    </th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">
                                        Item
                                    </th>
                                    @foreach($detailData['suppliers'] ?? [] as $supplierId => $supplierName)
                                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ $supplierName }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/10 bg-white dark:bg-gray-900">
                                @foreach($detailData['itemTypes'] ?? [] as $itemType)
                                    <tr>
                                        <td class="px-4 py-3 bg-gray-50 dark:bg-gray-800"></td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                            {{ $itemType }}
                                        </td>
                                        @foreach($detailData['suppliers'] ?? [] as $supplierId => $supplierName)
                                            <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-white">
                                                ${{ number_format($detailData['data'][$itemType][$supplierId] ?? 0, 2) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <!-- TOTAL row -->
                                <tr class="bg-gray-50 dark:bg-gray-800 font-bold">
                                    <td class="px-4 py-3 bg-gray-100 dark:bg-gray-700"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white font-bold">
                                        TOTAL
                                    </td>
                                    @foreach($detailData['suppliers'] ?? [] as $supplierId => $supplierName)
                                        <td class="px-4 py-3 text-right text-sm font-bold text-primary-600">
                                            ${{ number_format($detailData['totals'][$supplierId] ?? 0, 2) }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
