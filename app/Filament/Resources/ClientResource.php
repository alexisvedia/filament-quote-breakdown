<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $navigationLabel = 'Clients';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Básica')->schema([
                Forms\Components\TextInput::make('name')->label('Nombre de la Empresa')->required(),
                Forms\Components\TextInput::make('legal_name')->label('Razón Social'),
                Forms\Components\TextInput::make('tax_id')->label('RUC/ID Fiscal'),
                Forms\Components\TextInput::make('contact_name')->label('Nombre de Contacto'),
            ])->columns(2),
            Forms\Components\Section::make('Contacto')->schema([
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('phone')->label('Teléfono'),
                Forms\Components\TextInput::make('whatsapp'),
                Forms\Components\Textarea::make('address')->label('Dirección'),
                Forms\Components\TextInput::make('city')->label('Ciudad'),
                Forms\Components\TextInput::make('country')->label('País'),
            ])->columns(2),
            Forms\Components\Section::make('Configuración Comercial')->schema([
                Forms\Components\TextInput::make('credit_limit')->label('Límite de Crédito')->numeric(),
                Forms\Components\TextInput::make('payment_terms')->label('Términos de Pago'),
                Forms\Components\Select::make('currency')->label('Moneda')
                    ->options(['USD' => 'USD', 'EUR' => 'EUR', 'ARS' => 'ARS']),
                Forms\Components\Toggle::make('is_active')->label('Activo')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('country')
                    ->label('Country')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('city')
                    ->label('City'),
                Tables\Columns\BadgeColumn::make('currency')
                    ->label('Currency')
                    ->color('info'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
