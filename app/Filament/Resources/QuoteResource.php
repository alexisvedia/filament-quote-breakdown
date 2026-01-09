<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Quotes';
    protected static ?string $modelLabel = 'Quotation';
    protected static ?string $pluralModelLabel = 'Quotation Management';
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('client_id')
                ->label('Client')
                ->relationship('client', 'company')
                ->required()
                ->searchable()
                ->preload()
                ->helperText('Select the client first')
                ->live(),

            Forms\Components\Select::make('buyer_department')
                ->label('Buyer Department')
                ->options([
                    'mens' => "Men's",
                    'womens' => "Women's",
                    'kids' => "Kids",
                    'accessories' => 'Accessories',
                ])
                ->searchable()
                ->helperText('Select the buyer department'),

            Forms\Components\Select::make('techpack_id')
                ->label('Techpack')
                ->relationship('techpack', 'style_name')
                ->required()
                ->searchable()
                ->preload()
                ->helperText('Only approved techpacks from the selected client are displayed'),

            Forms\Components\Toggle::make('has_artwork_design')
                ->label('Techpack has artwork design')
                ->helperText('Enable this option if the selected techpacks include artwork design')
                ->default(false),

            Forms\Components\DatePicker::make('deadline')
                ->label('Deadline')
                ->required()
                ->helperText('Set a deadline to receive the quote'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('NO. RFQ')
                    ->formatStateUsing(fn ($state) => '#' . $state)
                    ->color('warning')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.company')
                    ->label('Customer')
                    ->icon('heroicon-m-user')
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('season')
                    ->label('Season')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-calendar'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->icon('heroicon-m-calendar'),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Delivery')
                    ->date('d/m/Y')
                    ->icon('heroicon-m-clock')
                    ->color('success'),
                                Tables\Columns\TextColumn::make('items_count')
                                                ->label('Tech Packs')
                                                                ->badge()
                                                                                ->icon('heroicon-m-document')
                                                                                                ->color('warning')
                                                                                                                ->counts('items'),
                Tables\Columns\TextColumn::make('total')
                    ->label('FOB Price')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' US$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('State')
                    
                    ->badge()
                                    ->icon(fn ($state) => match($state) {
                                                            'in_production' => 'heroicon-m-cog-6-tooth',
                                                                                'pending' => 'heroicon-m-clock',
                                                                                                    'completed' => 'heroicon-m-check-circle',
                                                                                                                        default => 'heroicon-m-question-mark-circle',
                                                                                                                                        })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'in_production' => 'In Production',
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        default => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'in_production' => 'success',
                        'pending' => 'warning',
                        'completed' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('see')
                    ->label('See')
                    ->icon('heroicon-m-eye')
                                        ->color('gray')
                    ->url(fn (Quote $record) => QuoteResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
