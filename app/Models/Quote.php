<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'delivery_date' => 'date',
        'deadline' => 'date',
    ];

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

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'quote_supplier')
            ->withPivot('status', 'invited_at', 'responded_at', 'deadline', 'invitation_message')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(QuoteMessage::class);
    }

    public function comments()
    {
        return $this->hasMany(QuoteComment::class);
    }
}
