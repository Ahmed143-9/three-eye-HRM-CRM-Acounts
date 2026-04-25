<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPackingListItem extends Model
{
    protected $table = 'sales_packing_list_items';

    protected $fillable = [
        'packing_list_id',
        'item_name',
        'description',
        'quantity',
        'unit',
    ];
}
