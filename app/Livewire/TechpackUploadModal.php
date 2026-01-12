<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Techpack;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class TechpackUploadModal extends Component
{
    use WithFileUploads;

    public bool $showModal = false;
    public $client_id = null;
    public $buyer_department = null;
    public $pdf_files = [];

    #[On('openTechpackUploadModal')]
    public function openModal(): void
    {
        $this->reset(['client_id', 'buyer_department', 'pdf_files']);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', id: 'techpack-upload-modal');
        $this->reset(['client_id', 'buyer_department', 'pdf_files']);
    }

    public function removeFile($index): void
    {
        if (isset($this->pdf_files[$index])) {
            unset($this->pdf_files[$index]);
            $this->pdf_files = array_values($this->pdf_files);
        }
    }

    public function uploadFiles(): void
    {
        if (empty($this->client_id)) {
            Notification::make()
                ->title('Please select a buyer')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->pdf_files)) {
            Notification::make()
                ->title('Please select at least one PDF file')
                ->danger()
                ->send();
            return;
        }

        $count = 0;
        foreach ($this->pdf_files as $file) {
            // Store the file
            $path = $file->store('techpacks', 'public');

            Techpack::create([
                'client_id' => $this->client_id,
                'design_image' => $path,
                'status' => 'under_review',
                'style_code' => 'TP-' . str_pad(Techpack::count() + 1, 3, '0', STR_PAD_LEFT) . '-' . date('Y'),
                'style_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'buyer_department' => $this->buyer_department,
            ]);
            $count++;
        }

        Notification::make()
            ->title($count . ' techpack(s) uploaded successfully')
            ->success()
            ->send();

        $this->closeModal();
        $this->dispatch('techpacks-uploaded');
    }

    public function getClientsProperty()
    {
        return Client::pluck('company', 'id');
    }

    public function getBuyerDepartmentsProperty(): array
    {
        if (!$this->client_id) {
            return [];
        }

        $client = Client::find($this->client_id);
        if (!$client) {
            return [];
        }

        // Departamentos por buyer (hardcodeado por ahora)
        // En el futuro esto podría venir de una tabla client_departments
        $departmentsByBuyer = [
            'ALLSAINTS' => [
                'ALLSAINTS - REGULAR' => 'ALLSAINTS - REGULAR',
                'GENTLEMEN' => 'Gentlemen',
                'LADIES' => 'Ladies',
                'KIDS' => 'Kids',
                'ACCESSORIES' => 'Accessories',
            ],
            // Agregar más buyers con departamentos aquí
        ];

        // Buscar si el nombre del buyer coincide con alguno que tenga departamentos
        foreach ($departmentsByBuyer as $buyerName => $departments) {
            if (stripos($client->company, $buyerName) !== false) {
                return $departments;
            }
        }

        return [];
    }

    public function updatedClientId(): void
    {
        // Reset department when buyer changes
        $this->buyer_department = null;
    }

    public function render()
    {
        return view('livewire.techpack-upload-modal');
    }
}
