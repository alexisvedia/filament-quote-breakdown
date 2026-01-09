<?php

namespace App\Filament\Resources\TechpackResource\Pages;

use App\Filament\Resources\TechpackResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTechpack extends EditRecord
{
    protected static string $resource = TechpackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
