<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
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
        'credit_limit',
        'client_category',
        'erp_code',
        'sync_status',
        'last_sync',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'last_sync' => 'datetime',
    ];

    public function techpacks(): HasMany
    {
        return $this->hasMany(Techpack::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
