<div class="flex items-center gap-3">
    <button
        type="button"
        x-data
        x-on:click="$dispatch('open-modal', { id: 'techpack-upload-modal' })"
        class="fi-btn fi-btn-size-md gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg border-2 border-orange-500 text-orange-500 hover:bg-orange-50 transition inline-flex items-center">
        <x-heroicon-m-plus class="w-4 h-4" />
        Upload techpack
    </button>
    <a href="{{ route('filament.admin.resources.quotes.create') }}"
        class="fi-btn fi-btn-size-md gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg border-2 border-orange-500 text-orange-500 hover:bg-orange-50 transition inline-flex items-center">
        <x-heroicon-m-plus class="w-4 h-4" />
        New Quote
    </a>

    <!-- Techpack Upload Modal (global) -->
    @livewire('techpack-upload-modal')
</div>
