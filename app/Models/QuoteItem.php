<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['quote_id', 'supplier_id', 'item_name', 'unit_price', 'quantity', 'total'];
    
        public function quote()
            {
                    return $this->belongsTo(Quote::class);
                        }
                        
                            public function supplier()
                                {
                                        return $this->belongsTo(Supplier::class);
                                            }
                                        }
