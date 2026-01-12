<div>
    <!-- Costsheet Section -->
    <div class="fi-ta">
        <div class="fi-ta-header-ctn">
            <div class="fi-ta-header-toolbar flex items-center justify-between gap-x-4 px-4 py-3 sm:px-6">
                <div>
                    <h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Costsheet
                    </h3>
                </div>
                <div class="flex items-center gap-x-3">
                    <div class="fi-ta-search-ctn">
                        <label class="fi-input-wrapper flex rounded-lg shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20 bg-white dark:bg-white/5">
                            <span class="fi-input-wrapper-prefix flex items-center gap-x-3 ps-3">
                                <x-heroicon-m-magnifying-glass class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                            </span>
                            <input type="search" placeholder="Search" class="fi-input block w-full border-none bg-transparent py-1.5 pe-3 text-base text-gray-950 placeholder:text-gray-400 focus:ring-0 dark:text-white dark:placeholder:text-gray-500 sm:text-sm sm:leading-6">
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="fi-ta-content divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10 rounded-xl border border-gray-200 dark:border-white/10">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6 sm:last-of-type:pe-6">
                            <span class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    Style
                                </span>
                                <x-heroicon-m-chevron-down class="h-4 w-4 text-gray-400" />
                            </span>
                        </th>
                        @foreach($costsheetData['exampleSuppliers'] as $supplierId => $supplierName)
                            <th class="fi-ta-header-cell px-3 py-3.5">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                    {{ $supplierName }}
                                </span>
                            </th>
                        @endforeach
                        <th class="fi-ta-header-cell px-3 py-3.5 sm:last-of-type:pe-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                    @foreach($costsheetData['techpacks'] as $techpack)
                    <tr class="fi-ta-row transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="fi-ta-cell px-3 py-4 sm:first-of-type:ps-6">
                            <div class="fi-ta-col-wrp">
                                <x-filament::badge color="warning" class="font-mono">
                                    # {{ $techpack['style_code'] }}
                                </x-filament::badge>
                            </div>
                        </td>
                        @foreach($costsheetData['exampleSuppliers'] as $supplierId => $supplierName)
                            <td class="fi-ta-cell px-3 py-4">
                                <div class="fi-ta-col-wrp">
                                    @php
                                        $priceData = $techpack['prices'][$supplierId] ?? null;
                                        $price = $priceData['price'] ?? null;
                                        $status = $priceData['status'] ?? null;
                                    @endphp

                                    @if($status === 'pending')
                                        <x-filament::badge color="warning" icon="heroicon-o-clock">
                                            Pending
                                        </x-filament::badge>
                                    @elseif($status === 'under_review')
                                        <span class="text-sm">
                                            <span class="text-primary-600">${{ number_format($price, 2) }}</span>
                                            <span class="text-gray-400 text-xs">(under review)</span>
                                        </span>
                                    @elseif($price)
                                        <span class="text-sm text-gray-950 dark:text-white">
                                            ${{ number_format($price, 2) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                        <td class="fi-ta-cell px-3 py-4 sm:last-of-type:pe-6">
                            <div class="fi-ta-col-wrp flex justify-end">
                                <button
                                    wire:click="openDetailModal"
                                    type="button"
                                    class="fi-link fi-link-size-sm inline-flex items-center justify-center gap-1 text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300"
                                >
                                    <x-heroicon-o-eye class="h-4 w-4" />
                                    View Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="fi-ta-footer border-t border-gray-200 dark:border-white/10">
            <div class="fi-ta-footer-ctn flex items-center justify-between gap-x-4 px-4 py-3 sm:px-6">
                <p class="fi-ta-footer-info text-sm text-gray-500 dark:text-gray-400">
                    1 to {{ count($costsheetData['techpacks']) }} of {{ count($costsheetData['techpacks']) }} results are shown
                </p>
                <div class="flex items-center gap-x-2">
                    <label class="text-sm text-gray-500 dark:text-gray-400">per page</label>
                    <select class="fi-select-input block w-auto rounded-lg border-none bg-transparent py-1 pe-8 ps-3 text-sm text-gray-950 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-primary-600 dark:text-white dark:ring-white/10">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- History Section -->
    <div class="fi-ta mt-8">
        <div class="fi-ta-header-ctn">
            <div class="fi-ta-header-toolbar px-4 py-3 sm:px-6">
                <div>
                    <h3 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Version History
                    </h3>
                    <p class="fi-ta-header-description text-sm text-gray-500 dark:text-gray-400">
                        Track changes to costsheet over time
                    </p>
                </div>
            </div>
        </div>

        @if(count($historyData) > 0)
            <div class="fi-ta-content divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 rounded-xl border border-gray-200 dark:border-white/10">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:first-of-type:ps-6">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Version</span>
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Supplier</span>
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5 text-right">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Total Cost</span>
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Saved By</span>
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Date</span>
                            </th>
                            <th class="fi-ta-header-cell px-3 py-3.5 sm:last-of-type:pe-6">
                                <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">Notes</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                        @foreach($historyData as $version)
                            <tr class="fi-ta-row transition duration-75 hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="fi-ta-cell px-3 py-4 sm:first-of-type:ps-6">
                                    <x-filament::badge color="info">
                                        v{{ $version['version_number'] }}
                                    </x-filament::badge>
                                </td>
                                <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">
                                    {{ $version['supplier'] }}
                                </td>
                                <td class="fi-ta-cell px-3 py-4 text-sm text-right font-semibold text-gray-950 dark:text-white">
                                    ${{ number_format($version['total_cost'], 2) }}
                                </td>
                                <td class="fi-ta-cell px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $version['user'] }}
                                </td>
                                <td class="fi-ta-cell px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $version['created_at']->format('M d, Y H:i') }}
                                </td>
                                <td class="fi-ta-cell px-3 py-4 sm:last-of-type:pe-6 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                    {{ $version['notes'] ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-filament::section>
                <div class="fi-ta-empty-state px-6 py-12">
                    <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                        <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                            <x-heroicon-o-clock class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400" />
                        </div>
                        <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            No version history
                        </h4>
                        <p class="fi-ta-empty-state-description text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Click "Save Version" to create the first snapshot of this costsheet.
                        </p>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity" wire:click="closeDetailModal"></div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full ring-1 ring-gray-950/5 dark:ring-white/10">
                <!-- Modal Header -->
                <div class="fi-modal-header flex px-6 pt-6">
                    <div class="flex-1">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Quotations > {{ $selectedTechpackCode }}
                        </p>
                        <h2 class="fi-modal-heading text-xl font-semibold text-gray-950 dark:text-white mt-1">
                            {{ $selectedTechpackCode }}
                        </h2>
                    </div>
                    <button wire:click="closeDetailModal" class="fi-modal-close-btn text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <x-heroicon-o-x-mark class="h-6 w-6" />
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="fi-modal-content px-6 py-4">
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
                        <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="fi-ta-header-cell px-3 py-3.5 bg-gray-100 dark:bg-gray-800 sm:first-of-type:ps-6">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-500 dark:text-gray-400">
                                            {{ $selectedTechpackCode }}
                                        </span>
                                    </th>
                                    <th class="fi-ta-header-cell px-3 py-3.5">
                                        <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Item
                                        </span>
                                    </th>
                                    @foreach($detailData['suppliers'] ?? [] as $supplierId => $supplierName)
                                        <th class="fi-ta-header-cell px-3 py-3.5 text-right">
                                            <span class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                                {{ $supplierName }}
                                            </span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                                @foreach($detailData['itemTypes'] ?? [] as $itemType)
                                    <tr class="fi-ta-row">
                                        <td class="fi-ta-cell px-3 py-4 bg-gray-50 dark:bg-gray-800/50 sm:first-of-type:ps-6"></td>
                                        <td class="fi-ta-cell px-3 py-4 text-sm text-gray-950 dark:text-white">
                                            {{ $itemType }}
                                        </td>
                                        @foreach($detailData['suppliers'] ?? [] as $supplierId => $supplierName)
                                            <td class="fi-ta-cell px-3 py-4 text-right text-sm text-gray-950 dark:text-white">
                                                ${{ number_format($detailData['data'][$itemType][$supplierId] ?? 0, 2) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <!-- TOTAL row -->
                                <tr class="fi-ta-row bg-gray-50 dark:bg-gray-800">
                                    <td class="fi-ta-cell px-3 py-4 bg-gray-100 dark:bg-gray-700 sm:first-of-type:ps-6"></td>
                                    <td class="fi-ta-cell px-3 py-4 text-sm font-bold text-gray-950 dark:text-white">
                                        TOTAL
                                    </td>
                                    @foreach($detailData['suppliers'] ?? [] as $supplierId => $supplierName)
                                        <td class="fi-ta-cell px-3 py-4 text-right text-sm font-bold text-warning-600">
                                            ${{ number_format($detailData['totals'][$supplierId] ?? 0, 2) }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="fi-modal-footer px-6 pb-6">
                    <div class="flex justify-end">
                        <x-filament::button color="gray" wire:click="closeDetailModal">
                            Close
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
