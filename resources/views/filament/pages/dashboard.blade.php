<x-filament-panels::page>
    @livewire(\App\Filament\Widgets\StatsOverview::class)
    
    <div class="mt-6">
        @livewire(\App\Filament\Widgets\LatestQuotationsWidget::class)
    </div>

    <div class="mt-6">
        @livewire(\App\Filament\Widgets\MessagesWidget::class)
    </div>
</x-filament-panels::page>
