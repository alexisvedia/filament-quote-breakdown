<?php

namespace App\Livewire;

use App\Models\Quote;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * MOCK COMPONENT - Solo UI Visual
 *
 * Este componente muestra una UI de chat con datos hardcodeados.
 * NO es funcional - solo sirve para demostrar como quedaria la interfaz.
 *
 * Para implementar funcionalidad real, considerar:
 * - adultdate/filament-messages (simple)
 * - jaocero/filachat (completo, requiere Reverb)
 */
class QuoteMessagesTab extends Component
{
    public Quote $quote;
    public ?int $selectedSupplierId = null;
    public string $newMessage = '';

    public function mount(): void
    {
        // Auto-select first supplier for demo
        $this->selectedSupplierId = 1;
    }

    public function selectSupplier(int $supplierId): void
    {
        $this->selectedSupplierId = $supplierId;
    }

    public function sendMessage(): void
    {
        // MOCK - No hace nada, solo limpia el input
        $this->newMessage = '';
    }

    public function render(): View
    {
        return view('livewire.quote-messages-tab');
    }
}
