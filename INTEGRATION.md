# Integration Guide - Step by Step

This guide provides detailed step-by-step instructions to integrate the Filament Quote Breakdown into your existing Laravel/Filament project.

## Prerequisites

- Laravel 10+ with Filament v3 installed
- Existing Quote, Style, and Supplier models
- Database migrations for quotes, styles, cost_items, suppliers, and junction tables

## Step 1: Copy Files

Copy the following files from this repository to your project:

```bash
# Copy the Page class
cp app/Filament/Resources/QuoteResource/Pages/StyleBreakdown.php \
   your-project/app/Filament/Resources/QuoteResource/Pages/StyleBreakdown.php

# Copy the Blade view
cp resources/views/filament/resources/quote-resource/pages/style-breakdown.blade.php \
   your-project/resources/views/filament/resources/quote-resource/pages/style-breakdown.blade.php
```

## Step 2: Register the Route

Open your `app/Filament/Resources/QuoteResource.php` and update the `getPages()` method:

```php
public static function getPages(): array
{
    return [
        'index' => Pages\ListQuotes::route('/'),
        'create' => Pages\CreateQuote::route('/create'),
        'view' => Pages\ViewQuote::route('/{record}'),
        'edit' => Pages\EditQuote::route('/{record}/edit'),
        
        // ADD THIS LINE:
        'style-breakdown' => Pages\StyleBreakdown::route('/{record}/styles/{style}/breakdown'),
    ];
}
```

## Step 3: Add Model Relationships

### Quote Model

```php
public function styles(): HasMany
{
    return $this->hasMany(Style::class);
}
```

### Style Model

```php
public function costItems(): HasMany
{
    return $this->hasMany(CostItem::class);
}

public function suppliers(): BelongsToMany
{
    return $this->belongsToMany(Supplier::class, 'style_supplier')
        ->withTimestamps();
}
```

### CostItem Model

```php
public function style(): BelongsTo
{
    return $this->belongsTo(Style::class);
}
```

## Step 4: Create Table Action Link

In your Styles table (or RelationManager), add an action to link to the breakdown page:

```php
use Filament\Tables\Actions\Action;

Action::make('view_breakdown')
    ->label('View Breakdown')
    ->icon('heroicon-m-chart-bar')
    ->url(fn (Style $record) => QuoteResource::getUrl(
        'style-breakdown',
        [
            'record' => $this->getOwnerRecord(),  // Parent Quote
            'style' => $record->getKey(),
        ]
    ))
    ->openUrlInNewTab(false),
```

If you're in a regular table (not RelationManager):

```php
Action::make('view_breakdown')
    ->label('View Breakdown')
    ->icon('heroicon-m-chart-bar')
    ->url(fn (Style $record) => QuoteResource::getUrl(
        'style-breakdown',
        [
            'record' => $record->quote_id,
            'style' => $record->getKey(),
        ]
    ))
    ->openUrlInNewTab(false),
```

## Step 5: Verify Database Structure

Ensure your database has these tables (see MODELS_EXAMPLE.md for full migrations):

- `quotes` - Contains quote data
- `styles` - Contains styles with quote_id FK
- `cost_items` - Contains items with style_id FK
- `suppliers` - Contains supplier data
- `style_supplier` - Junction table (style_id, supplier_id)
- `supplier_cost_items` - Cost and margin data per supplier per item

## Step 6: Test the Implementation

1. Navigate to your Filament admin panel
2. Go to Quotes
3. Select a Quote with Styles
4. Click on a Style's "View Breakdown" action
5. You should see:
   - Context information (Quote, Buyer, Style)
   - Itemized breakdown table with all suppliers
   - Totals section showing cost per supplier

## Common Customizations

### Change Currency

In `StyleBreakdown.php`, find the `table()` method:

```php
->money('eur', true)  // Change 'usd' to your currency
```

### Add More Columns

Modify the `supplierColumnGroups` in the `table()` method to include additional data:

```php
return ColumnGroup::make($supplier->name, [
    TextColumn::make("supplier_{$sid}_cost")
        ->label('Cost')
        ->money('usd', true),
    
    TextColumn::make("supplier_{$sid}_margin")
        ->label('Margin %')
        ->formatStateUsing(fn ($state) => filled($state) ? number_format((float) $state, 0) . '%' : '—'),
    
    // Add your custom columns here
]);
```

### Remove Grouping

If your items don't have categories, remove the grouping:

```php
->groups([])  // Empty array removes grouping
```

### Change Default Sort

In `getTableQuery()` method:

```php
return CostItem::query()
    ->where('style_id', $this->style->getKey())
    ->orderBy('your_field');  // Change sorting field
```

## Troubleshooting

### 404 Error When Accessing Breakdown Page

- Verify the route is registered in `QuoteResource.php`
- Check that both `record` (Quote ID) and `style` (Style ID) parameters are passed
- Ensure the Quote and Style exist in the database

### Empty Table

- Verify the Style has associated CostItems
- Verify the Style has associated Suppliers via the style_supplier pivot table
- Verify the SupplierCostItem records exist for the cost_items and suppliers

### Missing Costs/Margins

- Check the supplier_cost_items table has entries for all item-supplier combinations
- Verify the column names match: `cost_item_id`, `supplier_id`, `cost`, `margin_percent`

### Styling Issues

If the Filament components don't display correctly:

- Clear your cache: `php artisan cache:clear`
- Rebuild assets: `npm run build`
- Verify Filament is properly installed and configured

## Support

For issues or questions, refer to:
- `README.md` - Overview and features
- `MODELS_EXAMPLE.md` - Expected model and database structure
- Filament documentation: https://filamentphp.com/docs
