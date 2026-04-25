<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPI extends Model
{
    protected $table = 'sales_pis';

    protected $fillable = [
        'order_id',
        'pi_number',
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
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }
}
