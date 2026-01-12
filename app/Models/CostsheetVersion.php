<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CostsheetVersion extends Model
{
    protected $fillable = [
        'quote_id',
        'supplier_id',
        'user_id',
        'version_number',
        'items_snapshot',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'items_snapshot' => 'array',
        'total_cost' => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
