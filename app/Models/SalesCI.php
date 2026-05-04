<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesCI extends Model
{
    protected $table = 'sales_cis';

    protected $fillable = [
        'order_id',
        'pi_id',
        'lc_id',
        'ci_number',
        'client_ci_number',
        'ci_date',
        'lc_validity_date',
        'latest_shipment_date',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function tankers()
    {
        return $this->hasMany(SalesCITanker::class, 'ci_id');
    }

    public function packingList()
    {
        return $this->hasOne(SalesPackingList::class, 'ci_id');
    }

    public function consignmentNote()
    {
        return $this->hasOne(SalesConsignmentNote::class, 'ci_id');
    }

    public function delivery()
    {
        return $this->hasOne(SalesDelivery::class, 'ci_id');
    }

    public function transports()
    {
        return $this->hasMany(Transport::class, 'ci_id');
    }
}
