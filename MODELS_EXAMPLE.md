# Model Examples for Filament Quote Breakdown

These are example models that show the expected structure for this implementation.

## Quote Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'quotation_no',
        'buyer_name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function styles(): HasMany
    {
        return $this->hasMany(Style::class);
    }
}
```

## Style Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Style extends Model
{
    protected $fillable = [
        'quote_id',
        'code',
        'name',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function costItems(): HasMany
    {
        return $this->hasMany(CostItem::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'style_supplier')
            ->withTimestamps();
    }
}
```

## CostItem Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostItem extends Model
{
    protected $fillable = [
        'style_id',
        'category',
        'name',
        'sort_order',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class);
    }

    public function supplierCosts(): HasMany
    {
        return $this->hasMany(SupplierCostItem::class);
    }
}
```

## Supplier Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'email',
        'country',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function styles(): BelongsToMany
    {
        return $this->belongsToMany(Style::class, 'style_supplier')
            ->withTimestamps();
    }
}
```

## SupplierCostItem Model

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierCostItem extends Model
{
    protected $fillable = [
        'cost_item_id',
        'supplier_id',
        'cost',
        'margin_percent',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'margin_percent' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function costItem(): BelongsTo
    {
        return $this->belongsTo(CostItem::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
```

## Database Migrations

Here are the expected migrations:

### Quotes Table
```php
Schema::create('quotes', function (Blueprint $table) {
    $table->id();
    $table->string('quotation_no')->unique();
    $table->string('buyer_name')->nullable();
    $table->timestamps();
});
```

### Styles Table
```php
Schema::create('styles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quote_id')->constrained();
    $table->string('code')->nullable();
    $table->string('name');
    $table->timestamps();
});
```

### Cost Items Table
```php
Schema::create('cost_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('style_id')->constrained();
    $table->string('category')->nullable();
    $table->string('name');
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### Suppliers Table
```php
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->nullable();
    $table->string('country')->nullable();
    $table->timestamps();
});
```

### Style Supplier Pivot Table
```php
Schema::create('style_supplier', function (Blueprint $table) {
    $table->foreignId('style_id')->constrained()->onDelete('cascade');
    $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
    $table->primary(['style_id', 'supplier_id']);
    $table->timestamps();
});
```

### Supplier Cost Items Table
```php
Schema::create('supplier_cost_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cost_item_id')->constrained()->onDelete('cascade');
    $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
    $table->decimal('cost', 10, 2);
    $table->decimal('margin_percent', 5, 2)->default(0);
    $table->timestamps();

    $table->unique(['cost_item_id', 'supplier_id']);
});
```
