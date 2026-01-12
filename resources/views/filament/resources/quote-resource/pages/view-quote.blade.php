<x-filament-panels::page>
    {{-- Info Card --}}
    {{ $this->infolist }}

    {{-- Custom Tabs --}}
    <div class="mt-6">
        <div class="flex justify-center">
            <nav class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-800 p-1 space-x-1" aria-label="Tabs">
                <button 
                    wire:click="setActiveTab('styles')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'styles' ? 'bg-white dark:bg-gray-700 text-warning-600 shadow' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                >
                    <x-heroicon-o-squares-2x2 class="w-4 h-4 inline-block mr-1" />
                    Styles
                </button>
                <button 
                    wire:click="setActiveTab('costsheet')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'costsheet' ? 'bg-white dark:bg-gray-700 text-warning-600 shadow' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                >
                    Costsheet
                </button>
                <button 
                    wire:click="setActiveTab('samples')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'samples' ? 'bg-white dark:bg-gray-700 text-warning-600 shadow' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                >
                    <x-heroicon-o-user-group class="w-4 h-4 inline-block mr-1" />
                    Sample Orders
                </button>
                <button 
                    wire:click="setActiveTab('production')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'production' ? 'bg-white dark:bg-gray-700 text-warning-600 shadow' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                >
                    <x-heroicon-o-clipboard-document-list class="w-4 h-4 inline-block mr-1" />
                    Production Orders (PO)
                </button>
                <button
                    wire:click="setActiveTab('action_plan')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'action_plan' ? 'bg-white dark:bg-gray-700 text-warning-600 shadow' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                >
                    <x-heroicon-o-calendar-days class="w-4 h-4 inline-block mr-1" />
                    Action Plan and Progress
                </button>
                <button
                    wire:click="setActiveTab('suppliers')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'suppliers' ? 'bg-white dark:bg-gray-700 text-warning-600 shadow' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                >
                    <x-heroicon-o-users class="w-4 h-4 inline-block mr-1" />
                    Suppliers
                </button>
                <button
                    wire:click="setActiveTab('comments')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'comments' ? 'bg-white dark:bg-gray-700 text-warning-600 shadow' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                >
                    <x-heroicon-o-chat-bubble-bottom-center-text class="w-4 h-4 inline-block mr-1" />
                    Comments
                </button>
                <button
                    wire:click="setActiveTab('messages')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'messages' ? 'bg-white dark:bg-gray-700 text-warning-600 shadow' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400' }}"
                >
                    <x-heroicon-o-chat-bubble-left-right class="w-4 h-4 inline-block mr-1" />
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
