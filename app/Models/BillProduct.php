<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillProduct extends Model
{
    protected $fillable = [
        'product_id',
        'bill_id',
        'quantity',
        'tax',
        'discount',
        'price',
        'description',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\ProductService', 'id', 'product_id');
    }

    public function chartAccount()
    {
        return $this->hasOne('App\Models\ChartOfAccount', 'id', 'chart_account_id');
    }
}
