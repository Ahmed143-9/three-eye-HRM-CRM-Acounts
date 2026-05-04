<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesBuyingDetail extends Model
{
    protected $fillable = [
        'order_id',
        'supplier_id',
        'supplier_name',
        'total_amount',
        'status',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function items()
    {
        return $this->hasMany(SalesBuyingItem::class, 'buying_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
