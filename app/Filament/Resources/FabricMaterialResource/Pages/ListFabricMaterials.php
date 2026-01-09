<?php

namespace App\Filament\Resources\FabricMaterialResource\Pages;

use App\Filament\Resources\FabricMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFabricMaterials extends ListRecords
{
    protected static string $resource = FabricMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
