<x-filament::section>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">Requires Attention</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Focus on the most urgent quotes and supplier responses.</p>
        </div>
        <x-filament::button color="gray" icon="heroicon-o-arrow-right" tag="a" href="{{ \App\Filament\Resources\QuoteResource::getUrl('index') }}">
            View all quotes
        </x-filament::button>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-3">
        @foreach($cards as $card)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $card['title'] }}</p>
                    <x-filament::badge :color="$card['color']">{{ $card['count'] }}</x-filament::badge>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $card['description'] }}</p>
                <a class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-500 dark:text-primary-400" href="{{ $card['url'] }}">
                    {{ $card['action'] }}
                    <x-heroicon-o-arrow-right class="h-4 w-4" />
                </a>
            </div>
        @endforeach
    </div>
</x-filament::section>
