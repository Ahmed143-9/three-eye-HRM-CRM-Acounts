<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPOItem extends Model
{
    protected $table = 'sales_po_items';

    protected $fillable = [
        'po_id',
        'item_name',
        'description',
        'quantity',
        'unit_id',
        'price_per_unit',
        'currency_type',
        'total',
    ];
}
