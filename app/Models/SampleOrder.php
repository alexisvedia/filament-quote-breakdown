<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SampleOrder extends Model
{
    protected $fillable = [
        'quote_id',
        'techpack_id',
        'supplier_id',
        'tp_code',
        'requested_by',
        'request_date',
        'eta',
        'status',
        'sizes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'eta' => 'date',
        'sizes' => 'array',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function techpack(): BelongsTo
    {
        return $this->belongsTo(Techpack::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
