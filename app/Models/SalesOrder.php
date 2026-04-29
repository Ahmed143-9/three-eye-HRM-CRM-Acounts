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

    public function cis()
    {
        return $this->hasMany(SalesCI::class, 'order_id');
    }

    public function ci()
    {
        return $this->hasOne(SalesCI::class, 'order_id')->latestOfMany();
    }

    public function delivery()
    {
        return $this->hasOne(SalesDelivery::class, 'order_id')->latestOfMany();
    }
}
