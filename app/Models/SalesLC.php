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
        'client_lc_number',
        'amount',
        'lc_date',
        'latest_shipment_date',
        'lc_validity_date',
        'seller_name',
        'seller_address',
        'seller_mobile',
        'seller_email',
        'buyer_name',
        'buyer_address',
        'buyer_mobile',
        'buyer_email',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }
}
