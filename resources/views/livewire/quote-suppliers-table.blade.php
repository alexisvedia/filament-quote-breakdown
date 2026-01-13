<div>
    <h3 class="text-lg font-semibold mb-4">Suppliers</h3>
    <p class="text-sm text-gray-500 mb-4">Manage suppliers invited to this quotation and their status.</p>
    @php
        $summary = $this->getSummary();
    @endphp
    <x-filament::grid :default="1" :md="4" class="mb-6 gap-4">
        <x-filament::grid.column>
            <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs uppercase text-gray-500">Invited</p>
                <p class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">{{ $summary['total'] }}</p>
                <p class="text-xs text-gray-500">Total suppliers</p>
            </div>
        </x-filament::grid.column>
        <x-filament::grid.column>
            <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs uppercase text-gray-500">Responded</p>
                <p class="mt-2 text-xl font-semibold text-success-600">{{ $summary['responded'] }}</p>
                <p class="text-xs text-gray-500">Accepted or rejected</p>
            </div>
        </x-filament::grid.column>
        <x-filament::grid.column>
            <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs uppercase text-gray-500">Pending</p>
                <p class="mt-2 text-xl font-semibold text-warning-600">{{ $summary['pending'] }}</p>
                <p class="text-xs text-gray-500">Awaiting response</p>
            </div>
        </x-filament::grid.column>
        <x-filament::grid.column>
            <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs uppercase text-gray-500">Overdue</p>
                <p class="mt-2 text-xl font-semibold text-danger-600">{{ $summary['overdue'] }}</p>
                <p class="text-xs text-gray-500">Past deadline</p>
            </div>
        </x-filament::grid.column>
    </x-filament::grid>
    {{ $this->table }}
</div>
