<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteMessage extends Model
{
    protected $fillable = [
        'quote_id',
        'supplier_id',
        'user_id',
        'body',
        'is_from_wts',
        'is_read',
    ];

    protected $casts = [
        'is_from_wts' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
