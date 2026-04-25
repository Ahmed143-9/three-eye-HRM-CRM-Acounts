<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesLC extends Model
{
    protected $table = 'sales_lcs';

    protected $fillable = [
        'order_id',
        'pi_id',
        'lc_no',
        'amount',
        'lc_date',
        'latest_shipment_date',
        'lc_validity_date',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }
}
