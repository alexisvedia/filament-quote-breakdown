<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FabricMaterialResource\Pages;
use App\Filament\Resources\FabricMaterialResource\RelationManagers;
use App\Models\FabricMaterial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FabricMaterialResource extends Resource
{
    protected static ?string $model = FabricMaterial::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Administration';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFabricMaterials::route('/'),
            'create' => Pages\CreateFabricMaterial::route('/create'),
            'edit' => Pages\EditFabricMaterial::route('/{record}/edit'),
        ];
    }
}
