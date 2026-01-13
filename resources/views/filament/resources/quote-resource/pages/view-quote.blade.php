<x-filament-panels::page>
    {{-- Info Card --}}
    {{ $this->infolist }}

    @php
        $progress = $this->getSupplierProgress();
        $deadlineMeta = $this->getDeadlineMeta();

        $tabButtonBase = 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md transition-colors whitespace-nowrap';
        $tabActiveClass = 'bg-white dark:bg-gray-700 text-warning-600 shadow';
        $tabInactiveClass = 'text-gray-500 hover:text-gray-700 dark:text-gray-400';

        $deadlineClass = match ($deadlineMeta['status']) {
            'overdue' => 'text-danger-600 dark:text-danger-400',
            'today' => 'text-warning-600 dark:text-warning-400',
            'soon' => 'text-warning-600 dark:text-warning-400',
            'on_track' => 'text-success-600 dark:text-success-400',
            default => 'text-gray-500 dark:text-gray-400',
        };

        $progressColor = $progress['percent'] >= 80
            ? 'bg-success-500'
            : ($progress['percent'] >= 50 ? 'bg-warning-500' : 'bg-danger-500');
    @endphp

    <x-filament::section class="mt-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Deadline</p>
                <div class="mt-2 flex items-center gap-3">
                    <p class="text-lg font-semibold text-gray-950 dark:text-white">
                        {{ $deadlineMeta['date'] }}
                    </p>
                    <span class="text-sm font-medium {{ $deadlineClass }}">
                        {{ $deadlineMeta['label'] }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Delivery tracking for this quotation.
                </p>
            </div>
            <div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Supplier Progress</p>
                    <span class="text-sm font-semibold text-gray-950 dark:text-white">{{ $progress['percent'] }}%</span>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ $progress['responded'] }} of {{ $progress['total'] }} suppliers responded
                </p>
                <div class="mt-3 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-800">
                    <div class="h-2 rounded-full {{ $progressColor }}" style="width: {{ $progress['percent'] }}%"></div>
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Custom Tabs --}}
    <div class="mt-6">
        <div class="flex justify-center">
            <nav class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-gray-800 p-1 space-x-1" aria-label="Tabs">
                <button 
                    wire:click="setActiveTab('styles')"
                    class="{{ $tabButtonBase }} {{ $activeTab === 'styles' ? $tabActiveClass : $tabInactiveClass }}"
                >
                    <x-heroicon-o-squares-2x2 class="h-4 w-4 shrink-0" />
                    Styles
                </button>
                <button 
                    wire:click="setActiveTab('costsheet')"
                    class="{{ $tabButtonBase }} {{ $activeTab === 'costsheet' ? $tabActiveClass : $tabInactiveClass }}"
                >
                    Costsheet
                </button>
                <button
                    wire:click="setActiveTab('samples')"
                    class="{{ $tabButtonBase }} {{ $activeTab === 'samples' ? $tabActiveClass : $tabInactiveClass }}"
                >
                    <x-heroicon-o-user-group class="h-4 w-4 shrink-0" />
                    Sample Orders
                </button>
                <button
                    wire:click="setActiveTab('production')"
                    class="{{ $tabButtonBase }} {{ $activeTab === 'production' ? $tabActiveClass : $tabInactiveClass }}"
                >
                    <x-heroicon-o-clipboard-document-list class="h-4 w-4 shrink-0" />
                    Production Orders (PO)
                </button>
                <button
                    wire:click="setActiveTab('action_plan')"
                    class="{{ $tabButtonBase }} {{ $activeTab === 'action_plan' ? $tabActiveClass : $tabInactiveClass }}"
                >
                    <x-heroicon-o-calendar-days class="h-4 w-4 shrink-0" />
                    Action Plan and Progress
                </button>
                <button
                    wire:click="setActiveTab('suppliers')"
                    class="{{ $tabButtonBase }} {{ $activeTab === 'suppliers' ? $tabActiveClass : $tabInactiveClass }}"
                >
                    <x-heroicon-o-users class="h-4 w-4 shrink-0" />
                    Suppliers
                </button>
                <button
                    wire:click="setActiveTab('comments')"
                    class="{{ $tabButtonBase }} {{ $activeTab === 'comments' ? $tabActiveClass : $tabInactiveClass }}"
                >
                    <x-heroicon-o-chat-bubble-bottom-center-text class="h-4 w-4 shrink-0" />
                    Comments
                </button>
                <button
                    wire:click="setActiveTab('messages')"
                    class="{{ $tabButtonBase }} {{ $activeTab === 'messages' ? $tabActiveClass : $tabInactiveClass }}"
                >
                    <x-heroicon-o-chat-bubble-left-right class="h-4 w-4 shrink-0" />
                    Messages
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow p-6">
            @if($activeTab === 'styles')
                <livewire:quote-styles-table :quote="$record" />
            @elseif($activeTab === 'costsheet')
                <livewire:quote-costsheet-table :quote="$record" />
            @elseif($activeTab === 'samples')
                <livewire:quote-sample-orders-table :quoteId="$record->id" />
            @elseif($activeTab === 'production')
                <livewire:quote-production-orders-table :quote="$record" />
            @elseif($activeTab === 'action_plan')
                <livewire:quote-action-plan-table :quote="$record" />
            @elseif($activeTab === 'suppliers')
                <livewire:quote-suppliers-table :quote="$record" />
            @elseif($activeTab === 'comments')
                <livewire:quote-comments-tab :quote="$record" />
            @elseif($activeTab === 'messages')
                <livewire:quote-messages-tab :quote="$record" />
            @endif
        </div>
    </div>
</x-filament-panels::page>
