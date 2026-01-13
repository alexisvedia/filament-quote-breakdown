<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use App\Models\Supplier;
use App\Models\Techpack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

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
        if ($form->getOperation() === 'create') {
            return $form->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Client')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Quote Number')
                                ->prefix('#')
                                ->required(),
                            Forms\Components\Select::make('client_id')
                                ->label('Client')
                                ->relationship('client', 'company')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->helperText('Select the client first')
                                ->live(),
                            Forms\Components\Select::make('buyer_brand')
                                ->label('Buyer / Brand')
                                ->options(fn () => \App\Models\Client::query()->pluck('company', 'company')->toArray())
                                ->searchable()
                                ->placeholder('Select an option')
                                ->dehydrated(false),
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
                            Forms\Components\Select::make('quote_type')
                                ->label('Quote Type')
                                ->options([
                                    'standard' => 'Standard',
                                    'rush' => 'Rush',
                                    'bulk' => 'Bulk',
                                ])
                                ->placeholder('Select an option')
                                ->dehydrated(false),
                            Forms\Components\Select::make('season')
                                ->label('Season')
                                ->options(function () {
                                    $year = now()->year;
                                    $seasons = [];
                                    for ($y = $year; $y <= $year + 2; $y++) {
                                        $seasons["SS {$y}"] = "SS {$y} (Spring/Summer)";
                                        $seasons["AW {$y}"] = "AW {$y} (Autumn/Winter)";
                                    }
                                    return $seasons;
                                })
                                ->searchable()
                                ->required()
                                ->placeholder('Select an option'),
                            Forms\Components\DatePicker::make('date')
                                ->label('Creation Date'),
                            Forms\Components\DatePicker::make('deadline')
                                ->label('Deadline')
                                ->required()
                                ->helperText('Set a deadline to receive the quote'),
                        ])
                        ->columns(2),
                    Forms\Components\Wizard\Step::make('Styles')
                        ->schema([
                            Forms\Components\Select::make('techpack_id')
                                ->label('Primary Techpack')
                                ->relationship('techpack', 'style_name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->helperText('Select the main techpack for this quote'),
                            Forms\Components\Select::make('techpack_ids')
                                ->label('Additional Techpacks (mock)')
                                ->options(fn () => Techpack::query()->pluck('style_name', 'id')->toArray())
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Optional mock list for prototype')
                                ->dehydrated(false),
                            Forms\Components\Toggle::make('has_artwork_design')
                                ->label('Includes Artwork Design')
                                ->helperText('Indicates if techpacks include artwork design')
                                ->default(false)
                                ->dehydrated(false),
                        ])
                        ->columns(2),
                    Forms\Components\Wizard\Step::make('Suppliers')
                        ->schema([
                            Forms\Components\CheckboxList::make('supplier_ids')
                                ->label('Invite Suppliers (mock)')
                                ->options(fn () => Supplier::query()
                                    ->where('is_active', true)
                                    ->orderBy('company')
                                    ->pluck('company', 'id')
                                    ->toArray())
                                ->searchable()
                                ->bulkToggleable()
                                ->columns(2)
                                ->helperText('Visual selection only')
                                ->dehydrated(false)
                                ->columnSpanFull(),
                            Forms\Components\DatePicker::make('supplier_deadline')
                                ->label('Supplier Deadline')
                                ->helperText('Mock deadline for supplier responses')
                                ->dehydrated(false),
                            Forms\Components\Textarea::make('supplier_message')
                                ->label('Message to suppliers')
                                ->rows(3)
                                ->placeholder('Optional note for invited suppliers...')
                                ->dehydrated(false)
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Forms\Components\Wizard\Step::make('Review')
                        ->schema([
                            Forms\Components\Section::make('Summary')
                                ->schema([
                                    Forms\Components\Placeholder::make('summary_client')
                                        ->label('Client')
                                        ->content(fn (Get $get) => $get('client_id')
                                            ? 'Client ID: ' . $get('client_id')
                                            : 'Select a client'),
                                    Forms\Components\Placeholder::make('summary_techpacks')
                                        ->label('Techpacks')
                                        ->content(function (Get $get): string {
                                            $primary = $get('techpack_id') ? 'Primary selected' : 'No primary techpack';
                                            $extras = $get('techpack_ids') ? count($get('techpack_ids')) . ' extra' : 'No extra techpacks';
                                            return $primary . ' · ' . $extras;
                                        }),
                                    Forms\Components\Placeholder::make('summary_suppliers')
                                        ->label('Suppliers')
                                        ->content(fn (Get $get) => $get('supplier_ids')
                                            ? count($get('supplier_ids')) . ' selected'
                                            : 'No suppliers selected'),
                                ])
                                ->columns(2),
                        ]),
                ])
                    ->columnSpanFull(),
            ]);
        }

        return $form->schema([
            Forms\Components\Section::make('Basic Information')
                ->description('Main quote data')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Quote Number')
                        ->prefix('#')
                        ->required(),
                    Forms\Components\Select::make('client_id')
                        ->label('Client')
                        ->relationship('client', 'company')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->helperText('Select the client first')
                        ->live(),
                    Forms\Components\Select::make('quote_type')
                        ->label('Quote Type')
                        ->options([
                            'standard' => 'Standard',
                            'rush' => 'Rush',
                            'bulk' => 'Bulk',
                        ])
                        ->placeholder('Select an option')
                        ->dehydrated(false),
                    Forms\Components\DatePicker::make('date')
                        ->label('Creation Date'),
                    Forms\Components\DatePicker::make('delivery_date')
                        ->label('Delivery Deadline')
                        ->helperText('Deadline to receive quote from supplier'),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Pending',
                            'in_production' => 'In Production',
                            'completed' => 'Completed',
                        ])
                        ->required(),
                ])
                ->columns(2)
                ->collapsible()
                ->columnSpanFull(),
            Forms\Components\Section::make('Buyer Information')
                ->description('Buyer and season data')
                ->schema([
                    Forms\Components\Select::make('buyer_brand')
                        ->label('Buyer / Brand')
                        ->options(fn () => \App\Models\Client::query()->pluck('company', 'company')->toArray())
                        ->searchable()
                        ->placeholder('Select an option')
                        ->dehydrated(false),
                    Forms\Components\Select::make('season')
                        ->label('Season')
                        ->options(function () {
                            $year = now()->year;
                            $seasons = [];
                            for ($y = $year; $y <= $year + 2; $y++) {
                                $seasons["SS {$y}"] = "SS {$y} (Spring/Summer)";
                                $seasons["AW {$y}"] = "AW {$y} (Autumn/Winter)";
                            }
                            return $seasons;
                        })
                        ->searchable()
                        ->required()
                        ->placeholder('Select an option'),
                ])
                ->columns(2)
                ->collapsible()
                ->columnSpanFull(),
            Forms\Components\Section::make('Tech Packs')
                ->description('Select Tech Packs for this quote')
                ->schema([
                    Forms\Components\Select::make('techpack_ids')
                        ->label('Tech Packs')
                        ->options(fn () => Techpack::query()->pluck('style_name', 'id')->toArray())
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText('Only approved Tech Packs are shown.')
                        ->default(fn (?Quote $record) => $record?->techpack_id ? [$record->techpack_id] : [])
                        ->dehydrated(false),
                    Forms\Components\Hidden::make('techpack_id'),
                    Forms\Components\Toggle::make('has_artwork_design')
                        ->label('Includes Artwork Design')
                        ->helperText('Indicates if Tech Packs include artwork design (screen print, embroidery, etc.)')
                        ->default(false)
                        ->dehydrated(false),
                ])
                ->columns(2)
                ->collapsible()
                ->columnSpanFull(),
            Forms\Components\Section::make('Pricing & Costs')
                ->description('Price and margin information')
                ->schema([
                    Forms\Components\TextInput::make('total_quantity')
                        ->label('Total Quantity')
                        ->numeric()
                        ->helperText('Total garment quantity')
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('fob_price')
                        ->label('Factory Price (FOB)')
                        ->numeric()
                        ->prefix('US$')
                        ->helperText('Factory unit price'),
                ])
                ->columns(2)
                ->collapsible()
                ->columnSpanFull()
                ->visible(fn ($livewire) => $livewire instanceof Pages\EditQuote),
            Forms\Components\Section::make('Production Specifications')
                ->description('Production requirements and minimums')
                ->schema([
                    Forms\Components\TextInput::make('production_lead_time')
                        ->label('Production Lead Time')
                        ->suffix('days')
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('minimum_per_style')
                        ->label('Minimum per Style')
                        ->suffix('units')
                        ->dehydrated(false),
                    Forms\Components\Repeater::make('minimums_by_color')
                        ->label('Minimums by Color')
                        ->schema([
                            Forms\Components\TextInput::make('color')
                                ->label('Color'),
                            Forms\Components\TextInput::make('minimum_quantity')
                                ->label('Minimum quantity')
                                ->numeric(),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add color')
                        ->helperText('Specify minimum quantity required per color')
                        ->dehydrated(false)
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('minimums_by_fabric')
                        ->label('Minimums by Fabric')
                        ->schema([
                            Forms\Components\TextInput::make('fabric_type')
                                ->label('Fabric Type'),
                            Forms\Components\TextInput::make('minimum_quantity')
                                ->label('Minimum (e.g. 1 ton = 5000 garments)'),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add fabric')
                        ->helperText('Fabric minimums in roll/ton')
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible()
                ->columnSpanFull()
                ->visible(fn ($livewire) => $livewire instanceof Pages\EditQuote),
            Forms\Components\Section::make('Fabric Information')
                ->schema([
                    Forms\Components\Tabs::make('FabricTabs')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('Fabric Information')
                                ->schema([
                                    Forms\Components\Repeater::make('fabrics')
                                        ->label('Fabrics')
                                        ->schema([
                                            Forms\Components\TextInput::make('fabric_name')
                                                ->label('Fabric Name/Code'),
                                            Forms\Components\Select::make('construction')
                                                ->label('Construction')
                                                ->options([
                                                    'single_jersey' => 'Single Jersey',
                                                    'rib' => 'Rib',
                                                    'interlock' => 'Interlock',
                                                    'french_terry' => 'French Terry',
                                                ])
                                                ->placeholder('Select an option'),
                                            Forms\Components\TextInput::make('yarn_count')
                                                ->label('Yarn Count'),
                                            Forms\Components\TextInput::make('composition')
                                                ->label('Content / Composition'),
                                            Forms\Components\Select::make('dyeing_type')
                                                ->label('Dyeing Type')
                                                ->options([
                                                    'piece_dyed' => 'Piece Dyed',
                                                    'yarn_dyed' => 'Yarn Dyed',
                                                    'garment_dyed' => 'Garment Dyed',
                                                ])
                                                ->placeholder('Select an option'),
                                            Forms\Components\TextInput::make('weight')
                                                ->label('Weight'),
                                            Forms\Components\TextInput::make('special_finishes')
                                                ->label('Special Finishes')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(1)
                                        ->addActionLabel('Add fabric')
                                        ->dehydrated(false),
                                ]),
                            Forms\Components\Tabs\Tab::make('Sizes')
                                ->schema([
                                    Forms\Components\Placeholder::make('sizes_placeholder')
                                        ->content('Sizes configuration')
                                        ->columnSpanFull(),
                                ]),
                            Forms\Components\Tabs\Tab::make('Artwork')
                                ->schema([
                                    Forms\Components\Placeholder::make('artwork_placeholder')
                                        ->content('Artwork details')
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull()
                ->visible(fn ($livewire) => $livewire instanceof Pages\EditQuote),
            Forms\Components\Select::make('buyer_department')
                ->label('Buyer Department')
                ->options([
                    'mens' => "Men's",
                    'womens' => "Women's",
                    'kids' => "Kids",
                    'accessories' => 'Accessories',
                ])
                ->searchable()
                ->helperText('Select the buyer department')
                ->columnSpanFull()
                ->visible(fn ($livewire) => !($livewire instanceof Pages\EditQuote)),
            Forms\Components\Select::make('techpack_id')
                ->label('Techpack')
                ->relationship('techpack', 'style_name')
                ->required()
                ->searchable()
                ->preload()
                ->helperText('Only approved techpacks from the selected client are displayed')
                ->columnSpanFull()
                ->visible(fn ($livewire) => !($livewire instanceof Pages\EditQuote)),
            Forms\Components\Toggle::make('has_artwork_design')
                ->label('Techpack has artwork design')
                ->helperText('Enable this option if the selected techpacks include artwork design')
                ->default(false)
                ->dehydrated(false)
                ->columnSpanFull()
                ->visible(fn ($livewire) => !($livewire instanceof Pages\EditQuote)),
            Forms\Components\DatePicker::make('deadline')
                ->label('Deadline')
                ->required()
                ->helperText('Set a deadline to receive the quote')
                ->columnSpanFull()
                ->visible(fn ($livewire) => !($livewire instanceof Pages\EditQuote)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->orderByRaw(
                    "CASE 
                        WHEN delivery_date IS NULL THEN 2
                        WHEN delivery_date < ? THEN 0
                        ELSE 1
                    END, delivery_date ASC",
                    [now()->toDateString()]
                );
            })
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
                    ->label('Deadline')
                    ->date('M d, Y')
                    ->icon('heroicon-m-clock')
                    ->description(function (Quote $record): string {
                        $deadline = $record->delivery_date;
                        if (!$deadline) {
                            return 'No deadline';
                        }

                        $date = $deadline instanceof Carbon ? $deadline : Carbon::parse($deadline);
                        $diff = Carbon::today()->diffInDays($date->copy()->startOfDay(), false);

                        if ($diff < 0) {
                            return abs($diff) . 'd overdue';
                        }

                        if ($diff === 0) {
                            return 'Due today';
                        }

                        return $diff . 'd left';
                    })
                    ->color(function ($state): string {
                        if (!$state) {
                            return 'gray';
                        }

                        $date = $state instanceof Carbon ? $state : Carbon::parse($state);
                        if ($date->isPast()) {
                            return 'danger';
                        }

                        return $date->diffInDays(Carbon::today()) <= 3 ? 'warning' : 'success';
                    }),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Tech Packs')
                    ->badge()
                    ->icon('heroicon-m-document')
                    ->color('warning')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('fob_price')
                    ->label('FOB Unit')
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0, 2) . ' US$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('FOB Total')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' US$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('State')

                    ->badge()
                    ->icon(fn ($state) => match($state) {
                        'approved' => 'heroicon-m-check-circle',
                        'in_production' => 'heroicon-m-cog-6-tooth',
                        'pending' => 'heroicon-m-clock',
                        'completed' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'approved' => 'Approved',
                        'in_production' => 'In Production',
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        default => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'approved' => 'success',
                        'in_production' => 'success',
                        'pending' => 'warning',
                        'completed' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('see')
                    ->label('View Details')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->url(fn (Quote $record) => QuoteResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([
                BulkAction::make('send_reminders')
                    ->label('Send reminders')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(fn () => null),
                BulkAction::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => null),
            ])
            ->emptyStateHeading('No quotations yet')
            ->emptyStateDescription('Create a quotation to start tracking quotes and suppliers.')
            ->emptyStateIcon('heroicon-o-document-text');
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
