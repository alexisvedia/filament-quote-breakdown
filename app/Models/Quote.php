<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function techpack()
    {
        return $this->belongsTo(Techpack::class);
    }

        public function supplier()
            {
                    return $this->belongsTo(Supplier::class);
                        }
}
