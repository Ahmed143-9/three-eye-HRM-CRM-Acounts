<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'current_step',
        'status',
        'workflow_data',
        'tankers_data',
        'created_by',
    ];

    protected $casts = [
        'workflow_data' => 'array',
        'tankers_data' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Client::class, 'customer_id');
    }

    public function po()
    {
        return $this->hasOne(SalesPO::class, 'order_id');
    }

    public function pi()
    {
        return $this->hasOne(SalesPI::class, 'order_id');
    }

    public function lc()
    {
        return $this->hasOne(SalesLC::class, 'order_id');
    }

    public function ci()
    {
        return $this->hasOne(SalesCI::class, 'order_id');
    }

    public function packingList()
    {
        return $this->hasOne(SalesPackingList::class, 'order_id');
    }

    public function consignmentNote()
    {
        return $this->hasOne(SalesConsignmentNote::class, 'order_id');
    }
}
