{{--
    MOCK UI - Solo Visual
    Este componente muestra datos hardcodeados para demostrar la UI.
    NO es funcional. Para implementar, usar:
    - adultdate/filament-messages
    - jaocero/filachat
--}}

@php
    // Mock data - Proveedores con conversaciones
    $mockSuppliers = [
        [
            'id' => 1,
            'company' => 'Textile Manufacturers Ltd',
            'email' => 'robert@textilemanufacturers.com',
            'last_message' => 'Thank you for the update. We will review...',
            'time' => '2 hours ago',
            'unread' => 0,
            'is_selected' => $selectedSupplierId === 1,
        ],
        [
            'id' => 2,
            'company' => 'Global Fabrics Inc',
            'email' => 'sales@globalfabrics.com',
            'last_message' => 'We can offer a 5% discount for bulk orders',
            'time' => '5 hours ago',
            'unread' => 2,
            'is_selected' => $selectedSupplierId === 2,
        ],
        [
            'id' => 3,
            'company' => 'Confecciones del Sur',
            'email' => 'ventas@confeccionesdelsur.com',
            'last_message' => 'Adjunto el costsheet actualizado',
            'time' => 'Yesterday',
            'unread' => 0,
            'is_selected' => $selectedSupplierId === 3,
        ],
        [
            'id' => 4,
            'company' => 'Damir Trading SAC',
            'email' => 'info@damirtrading.com',
            'last_message' => null,
            'time' => null,
            'unread' => 0,
            'is_selected' => $selectedSupplierId === 4,
        ],
    ];

    // Mock messages for selected supplier
    $mockMessages = [
        [
            'id' => 1,
            'body' => 'Hello! We have reviewed your RFQ #834893 for the mens collection. We are interested in providing a quote.',
            'is_from_wts' => false,
            'sender' => 'Robert Chen',
            'time' => 'Jan 08, 09:30',
            'is_read' => true,
            'attachments' => [],
        ],
        [
            'id' => 2,
            'body' => 'Great to hear! Please review the techpack specifications and let us know if you have any questions about the materials or construction details.',
            'is_from_wts' => true,
            'sender' => 'Admin',
            'time' => 'Jan 08, 10:15',
            'is_read' => true,
            'attachments' => [
                ['name' => 'Techpack_TP-002.pdf', 'size' => '1.3 MB', 'type' => 'pdf'],
            ],
        ],
        [
            'id' => 3,
            'body' => 'We have a few questions about the fabric composition. Is the 60% cotton / 40% polyester blend mandatory, or can we propose alternatives?',
            'is_from_wts' => false,
            'sender' => 'Robert Chen',
            'time' => 'Jan 08, 14:22',
            'is_read' => true,
            'attachments' => [],
        ],
        [
            'id' => 4,
            'body' => 'The blend is preferred but we are open to alternatives if they meet the quality standards. Please include any alternative proposals in your costsheet with a comparison.',
            'is_from_wts' => true,
            'sender' => 'Admin',
            'time' => 'Jan 08, 15:45',
            'is_read' => true,
            'attachments' => [],
        ],
        [
            'id' => 5,
            'body' => 'Thank you for the update. We will review the specs and send our costsheet by Friday. We can also include samples if needed.',
            'is_from_wts' => false,
            'sender' => 'Robert Chen',
            'time' => 'Jan 09, 11:30',
            'is_read' => true,
            'attachments' => [
                ['name' => 'Costsheet_v3.xlsx', 'size' => '420 KB', 'type' => 'sheet'],
                ['name' => 'Fabric_Swatch.jpg', 'size' => '1.2 MB', 'type' => 'image'],
            ],
        ],
    ];

    $selectedSupplier = collect($mockSuppliers)->firstWhere('id', $selectedSupplierId);
@endphp

<div class="flex h-[600px] border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
    {{-- Left Panel: Supplier Threads --}}
    <div class="w-72 flex-shrink-0 border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex flex-col">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-warning-500" />
                Messages
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Conversations with suppliers</p>
        </div>

        {{-- Supplier List --}}
        <div class="flex-1 overflow-y-auto">
            @foreach($mockSuppliers as $supplier)
                <button
                    wire:click="selectSupplier({{ $supplier['id'] }})"
                    class="w-full p-4 text-left transition-all duration-150 border-b border-gray-100 dark:border-gray-700
                        {{ $supplier['is_selected']
                            ? 'bg-warning-50 dark:bg-warning-900/20 border-l-4 border-l-warning-500'
                            : 'hover:bg-gray-100 dark:hover:bg-gray-700/50 border-l-4 border-l-transparent' }}"
                >
                    <div class="flex items-start gap-3">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-warning-400 to-warning-600 flex items-center justify-center shadow-sm">
                            <span class="text-white font-semibold text-sm">
                                {{ strtoupper(substr($supplier['company'], 0, 2)) }}
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-medium text-gray-900 dark:text-white truncate text-sm">
                                    {{ $supplier['company'] }}
                                </span>
                                @if($supplier['unread'] > 0)
                                    <span class="flex-shrink-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold bg-danger-500 text-white rounded-full">
                                        {{ $supplier['unread'] }}
                                    </span>
                                @endif
                            </div>

                            @if($supplier['last_message'])
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                    {{ Str::limit($supplier['last_message'], 35) }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    {{ $supplier['time'] }}
                                </p>
                            @else
                                <p class="text-sm text-gray-400 dark:text-gray-500 italic mt-0.5">No messages yet</p>
                            @endif
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Right Panel: Conversation --}}
    <div class="flex-1 flex flex-col bg-white dark:bg-gray-900">
        @if($selectedSupplier)
            {{-- Chat Header --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-warning-400 to-warning-600 flex items-center justify-center shadow-sm">
                        <span class="text-white font-semibold text-sm">
                            {{ strtoupper(substr($selectedSupplier['company'], 0, 2)) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $selectedSupplier['company'] }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            <x-heroicon-o-envelope class="w-3.5 h-3.5" />
                            {{ $selectedSupplier['email'] }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-success-500 mr-1.5"></span>
                            Online
                        </span>
                    </div>
                </div>
            </div>

            {{-- Messages Area --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50/50 dark:bg-gray-900/50" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%239C92AC\' fill-opacity=\'0.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                @foreach($mockMessages as $message)
                    <div class="flex {{ $message['is_from_wts'] ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[75%] {{ $message['is_from_wts'] ? 'order-2' : 'order-1' }}">
                            {{-- Message Bubble --}}
                            <div class="{{ $message['is_from_wts']
                                ? 'bg-warning-500 text-white rounded-2xl rounded-br-md'
                                : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-2xl rounded-bl-md shadow-sm border border-gray-100 dark:border-gray-700' }}
                                px-4 py-2.5">
                                <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message['body'] }}</p>
                                @if(!empty($message['attachments']))
                                    <div class="mt-3 space-y-2">
                                        @foreach($message['attachments'] as $file)
                                            @php
                                                $fileIcon = match ($file['type']) {
                                                    'image' => 'heroicon-o-photo',
                                                    'sheet' => 'heroicon-o-table-cells',
                                                    default => 'heroicon-o-document-text',
                                                };
                                            @endphp
                                            <div class="flex items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-xs text-white/90 {{ $message['is_from_wts'] ? '' : 'border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                                <x-dynamic-component :component="$fileIcon" class="h-4 w-4" />
                                                <span class="flex-1 truncate">{{ $file['name'] }}</span>
                                                <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $file['size'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Message Meta --}}
                            <div class="flex items-center gap-1.5 mt-1 px-1 {{ $message['is_from_wts'] ? 'justify-end' : 'justify-start' }}">
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $message['sender'] }}
                                </span>
                                <span class="text-xs text-gray-300 dark:text-gray-600">-</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $message['time'] }}
                                </span>
                                @if($message['is_from_wts'] && $message['is_read'])
                                    <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-success-400" />
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Input Area --}}
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <form wire:submit="sendMessage" class="flex items-end gap-3">
                    {{-- Attachment Button --}}
                    <button type="button" class="flex-shrink-0 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <x-heroicon-o-paper-clip class="w-5 h-5" />
                    </button>

                    {{-- Input --}}
                    <div class="flex-1">
                        <textarea
                            wire:model="newMessage"
                            rows="1"
                            class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-warning-500 focus:ring-warning-500 resize-none text-sm py-2.5 px-4"
                            placeholder="Type your message..."
                            style="min-height: 42px; max-height: 120px;"
                        ></textarea>
                    </div>

                    {{-- Send Button --}}
                    <button
                        type="submit"
                        class="flex-shrink-0 p-2.5 bg-warning-500 text-white rounded-xl hover:bg-warning-600 focus:outline-none focus:ring-2 focus:ring-warning-500 focus:ring-offset-2 transition-all duration-150 shadow-sm hover:shadow"
                    >
                        <x-heroicon-o-paper-airplane class="w-5 h-5" />
                    </button>
                </form>
            </div>
        @else
            {{-- No Supplier Selected --}}
            <div class="flex-1 flex items-center justify-center text-gray-500 dark:text-gray-400">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Select a conversation</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose a supplier from the left panel to view messages.</p>
                </div>
            </div>
        @endif
    </div>
</div>
