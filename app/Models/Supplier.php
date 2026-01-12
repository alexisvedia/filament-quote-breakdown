<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'company',
        'legal_name',
        'tax_id',
        'contact_name',
        'email',
        'phone',
        'website',
        'country',
        'region',
        'city',
        'address',
        'postal_code',
        'currency',
        'payment_terms',
        'supplier_category',
        'capabilities',
        'lead_time_days',
        'minimum_order_value',
        'erp_code',
        'sync_status',
        'last_sync',
        'is_active',
    ];

    protected $casts = [
        'last_sync' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function quotes()
    {
        return $this->belongsToMany(Quote::class, 'quote_supplier')
            ->withPivot('status', 'invited_at', 'responded_at', 'deadline', 'invitation_message')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(QuoteMessage::class);
    }
}
