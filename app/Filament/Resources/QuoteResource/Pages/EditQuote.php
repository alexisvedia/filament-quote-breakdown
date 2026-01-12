<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\Url;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected static string $view = 'filament.resources.quote-resource.pages.edit-quote';

    #[Url]
    public string $activeTab = 'techpacks';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')
                ->label('View')
                ->color('gray')
                ->url(fn () => QuoteResource::getUrl('view', ['record' => $this->record])),
            Actions\DeleteAction::make(),
        ];
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }
}
