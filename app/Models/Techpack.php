<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Techpack extends Model
{
    protected $fillable = [
        'style_code',
        'style_name',
        'design_image',
        'status',
        'client_id',
        'buyer',
        'buyer_department',
        'buyer_style_reference',
        'product_group',
        'sub_category',
        'our_contact',
        'purchase_uom',
        'season',
        'style_lead_time',
        'minimum_order_quantity',
        'style_embellishment',
        'construction',
        'content',
        'weight',
        'dyeing_type',
        'yarn_count',
        'width',
        'fabric_article_code',
        'special_finishes',
        'colors',
        'base_size',
        'sizes',
        'sketch',
        'front_artwork',
        'front_technique',
        'back_artwork',
        'back_technique',
        'sleeve_artwork',
        'sleeve_technique',
        'color',
        'dyed_process',
        'initial_request_date',
        'sms_x_date',
        'sms_comments',
        'pp_sample',
        'factory_price',
        'profit_margin',
        'wfx_style_code',
        'wfx_id',
        'wfx_last_sync',
    ];

    protected $casts = [
        'colors' => 'array',
        'sizes' => 'array',
        'style_embellishment' => 'boolean',
        'initial_request_date' => 'date',
        'sms_x_date' => 'date',
        'factory_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'wfx_last_sync' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
