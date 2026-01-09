<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechpackResource\Pages;
use App\Models\Techpack;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TechpackResource extends Resource
{
    protected static ?string $model = Techpack::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Techpack')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General Info')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Section::make('General Info')
                                ->schema([
                                    Forms\Components\TextInput::make('style_code')->required()->unique(ignoreRecord: true),
                                    Forms\Components\TextInput::make('style_name')->required(),
                                    Forms\Components\Select::make('status')
                                        ->options(['under_review' => 'Under Review', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                                        ->default('under_review'),
                                    Forms\Components\FileUpload::make('design_image')->image()->directory('techpacks'),
                                ])->columns(2),
                            Forms\Components\Section::make('Buyer Information')
                                ->schema([
                                    Forms\Components\Select::make('client_id')->relationship('client', 'company')->searchable()->preload(),
                                    Forms\Components\TextInput::make('buyer'),
                                    Forms\Components\TextInput::make('buyer_department'),
                                    Forms\Components\TextInput::make('buyer_style_reference'),
                                ])->columns(2),
                            Forms\Components\Section::make('Product Details')
                                ->schema([
                                    Forms\Components\Select::make('product_group')->options(['Tops' => 'Tops', 'Bottoms' => 'Bottoms', 'Dresses' => 'Dresses']),
                                    Forms\Components\TextInput::make('sub_category'),
                                    Forms\Components\TextInput::make('our_contact'),
                                    Forms\Components\Select::make('purchase_uom')->options(['Pieces' => 'Pieces', 'Dozens' => 'Dozens'])->default('Pieces'),
                                    Forms\Components\TextInput::make('season'),
                                    Forms\Components\TextInput::make('style_lead_time')->numeric()->suffix('days'),
                                    Forms\Components\TextInput::make('minimum_order_quantity')->numeric(),
                                    Forms\Components\Toggle::make('style_embellishment'),
                                ])->columns(4),
                        ]),

                    Forms\Components\Tabs\Tab::make('Fabric Details')
                        ->icon('heroicon-o-swatch')
                        ->schema([
                            Forms\Components\Section::make('Fabric Technical Details')
                                ->schema([
                                    Forms\Components\TextInput::make('construction'),
                                    Forms\Components\TextInput::make('content'),
                                    Forms\Components\TextInput::make('weight'),
                                    Forms\Components\Select::make('dyeing_type')->options(['Piece Dye' => 'Piece Dye', 'Yarn Dye' => 'Yarn Dye', 'Garment Dye' => 'Garment Dye']),
                                    Forms\Components\TextInput::make('yarn_count'),
                                    Forms\Components\TextInput::make('width')->suffix('cm'),
                                    Forms\Components\TextInput::make('fabric_article_code'),
                                    Forms\Components\Textarea::make('special_finishes'),
                                    Forms\Components\TagsInput::make('colors'),
                                ])->columns(3),
                        ]),

                    Forms\Components\Tabs\Tab::make('Sizes')
                        ->icon('heroicon-o-arrows-pointing-out')
                        ->schema([
                            Forms\Components\Section::make('Available Sizes')
                                ->schema([
                                    Forms\Components\Select::make('base_size')->options(['XS' => 'XS', 'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL']),
                                    Forms\Components\CheckboxList::make('sizes')
                                        ->options(['XXS' => 'XXS', 'XS' => 'XS', 'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL', 'XXL' => 'XXL', '3XL' => '3XL'])
                                        ->columns(4),
                                ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Artwork')
                        ->icon('heroicon-o-paint-brush')
                        ->schema([
                            Forms\Components\Section::make('Design and Sketch')
                                ->schema([Forms\Components\FileUpload::make('sketch')->image()]),
                            Forms\Components\Section::make('Artwork Details')
                                ->schema([
                                    Forms\Components\FileUpload::make('front_artwork')->image(),
                                    Forms\Components\TextInput::make('front_technique')->placeholder('e.g. Screen Print, Embroidery'),
                                    Forms\Components\FileUpload::make('back_artwork')->image(),
                                    Forms\Components\TextInput::make('back_technique'),
                                    Forms\Components\FileUpload::make('sleeve_artwork')->image(),
                                    Forms\Components\TextInput::make('sleeve_technique'),
                                ])->columns(2),
                            Forms\Components\Section::make('Color and Process')
                                ->schema([
                                    Forms\Components\TextInput::make('color'),
                                    Forms\Components\TextInput::make('dyed_process'),
                                    Forms\Components\DatePicker::make('initial_request_date'),
                                    Forms\Components\DatePicker::make('sms_x_date'),
                                    Forms\Components\Textarea::make('sms_comments'),
                                    Forms\Components\TextInput::make('pp_sample'),
                                ])->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('Costsheet')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Forms\Components\Section::make('Prices and Margins')
                                ->schema([
                                    Forms\Components\TextInput::make('factory_price')->numeric()->prefix('$'),
                                    Forms\Components\TextInput::make('profit_margin')->numeric()->suffix('%'),
                                ])->columns(2),
                        ]),

                    Forms\Components\Tabs\Tab::make('WFX Information')
                        ->icon('heroicon-o-cloud')
                        ->schema([
                            Forms\Components\Section::make('Synchronization with WFX')
                                ->schema([
                                    Forms\Components\TextInput::make('wfx_style_code'),
                                    Forms\Components\TextInput::make('wfx_id'),
                                    Forms\Components\DateTimePicker::make('wfx_last_sync')->disabled(),
                                ])->columns(3),
                        ]),
                ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('style_code')
                    ->badge()->color('warning')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('style_name')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('client.company')->label('Customer'),
                Tables\Columns\TextColumn::make('buyer')->badge()->color('info'),
                Tables\Columns\TextColumn::make('buyer_department'),
                Tables\Columns\TextColumn::make('season')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('sub_category')->badge(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'under_review', 'success' => 'approved', 'danger' => 'rejected']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['under_review' => 'Under Review', 'approved' => 'Approved']),
                Tables\Filters\SelectFilter::make('client_id')->relationship('client', 'company'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTechpacks::route('/'),
            'create' => Pages\CreateTechpack::route('/create'),
            'edit' => Pages\EditTechpack::route('/{record}/edit'),
        ];
    }
}
