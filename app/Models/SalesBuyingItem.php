<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesBuyingItem extends Model
{
    protected $fillable = [
        'buying_id',
        'item_name',
        'description',
        'quantity',
        'unit',
        'price',
        'total',
    ];

    public function buying()
    {
        return $this->belongsTo(SalesBuyingDetail::class, 'buying_id');
    }
}
