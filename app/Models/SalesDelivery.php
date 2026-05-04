<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesDelivery extends Model
{
    protected $fillable = [
        'order_id',
        'ci_id',
        'delivery_mode',
        'packing_type',
        'total_quantity_mt',
        'total_quantity_kg',
        'required_units',
        'drum_qty',
        'drum_unit',
        'drum_buying_price',
        'drum_buying_total',
        'drum_selling_price',
        'drum_selling_total',
        'created_by'
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function ci()
    {
        return $this->belongsTo(SalesCI::class, 'ci_id');
    }
}
