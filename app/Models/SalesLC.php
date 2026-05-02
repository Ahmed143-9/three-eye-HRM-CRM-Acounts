<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesLC extends Model
{
    protected $table = 'sales_lcs';

    protected $fillable = [
        'order_id',
        'pi_id',
        'lc_reference_no',
        'client_lc_no',
        'lc_type',
        'lc_qty',
        'unit',
        'lc_date',
        'latest_shipment_date',
        'lc_validity_date',
        'lifting_time',
        'country_of_origin',
        'tolerance',
        'port_of_loading',
        'port_of_discharge',
        'terms_and_conditions',
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
