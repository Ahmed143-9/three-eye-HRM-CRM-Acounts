<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPO extends Model
{
    protected $table = 'sales_pos';

    protected $fillable = [
        'order_id',
        'client_name',
        'client_address',
        'client_email',
        'client_phone',
        'grand_total',
        'signature',
        'hs_code',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function items()
    {
        return $this->hasMany(SalesPOItem::class, 'po_id');
    }
}
