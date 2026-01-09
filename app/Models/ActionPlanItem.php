<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionPlanItem extends Model
{
    protected $fillable = [
        'quote_id',
        'landmark',
        'plan_date',
        'real_date',
        'state',
    ];

    protected $casts = [
        'plan_date' => 'date',
        'real_date' => 'date',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function getDeltaDaysAttribute(): ?int
    {
        if (!$this->plan_date || !$this->real_date) {
            return null;
        }
        return $this->plan_date->diffInDays($this->real_date, false);
    }

    public function getLandmarkLabelAttribute(): string
    {
        return ucfirst($this->landmark);
    }
}
