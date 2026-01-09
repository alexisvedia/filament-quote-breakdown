<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('company')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('legal_name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('tax_id')
                        ->label('Tax ID'),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                ])->columns(2),

            Forms\Components\Section::make('Contact')
                ->schema([
                    Forms\Components\TextInput::make('contact_name'),
                    Forms\Components\TextInput::make('email')->email(),
                    Forms\Components\TextInput::make('phone')->tel(),
                    Forms\Components\TextInput::make('website')->url(),
                ])->columns(2),

            Forms\Components\Section::make('Location')
                ->schema([
                    Forms\Components\TextInput::make('country'),
                    Forms\Components\TextInput::make('region'),
                    Forms\Components\TextInput::make('city'),
                    Forms\Components\Textarea::make('address')->rows(2),
                    Forms\Components\TextInput::make('postal_code'),
                ])->columns(3),

            Forms\Components\Section::make('Commercial')
                ->schema([
                    Forms\Components\Select::make('currency')
                        ->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP'])
                        ->default('USD'),
                    Forms\Components\TextInput::make('payment_terms'),
                    Forms\Components\Select::make('supplier_category')
                        ->options([
                            'Fabric' => 'Fabric',
                            'CMT' => 'CMT',
                            'Full Package' => 'Full Package',
                            'Trims' => 'Trims',
                        ]),
                    Forms\Components\Textarea::make('capabilities')->rows(2),
                    Forms\Components\TextInput::make('lead_time_days')
                        ->numeric()
                        ->suffix('days'),
                    Forms\Components\TextInput::make('minimum_order_value')
                        ->numeric()
                        ->prefix('$'),
                ])->columns(2),

            Forms\Components\Section::make('ERP Sync')
                ->schema([
                    Forms\Components\TextInput::make('erp_code'),
                    Forms\Components\Select::make('sync_status')
                        ->options(['pending' => 'Pending', 'synced' => 'Synced', 'error' => 'Error'])
                        ->default('pending'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company')
                    ->icon('heroicon-o-truck')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\BadgeColumn::make('supplier_category'),
                Tables\Columns\TextColumn::make('lead_time_days')
                    ->suffix(' days'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier_category'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
