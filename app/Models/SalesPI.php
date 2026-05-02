<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPI extends Model
{
    protected $table = 'sales_pis';

    protected $fillable = [
        'order_id',
        'pi_number',
        'client_pi_number',
        'pi_date',
        'validity',
        'lifting_time',
        'payment_terms',
        'hs_code',
        'country_of_origin',
        'tolerance',
        'port_of_loading',
        'port_of_discharge',
        'amount',
        'seller_name',
        'seller_address',
        'seller_mobile',
        'seller_email',
        'buyer_name',
        'buyer_address',
        'buyer_mobile',
        'buyer_email',
        'incoterm',
        'bank_name',
        'account_name',
        'branch',
        'account_no',
        'swift_code',
        'terms_and_conditions',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }
}
