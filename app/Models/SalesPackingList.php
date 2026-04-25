<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPackingList extends Model
{
    protected $table = 'sales_packing_lists';

    protected $fillable = [
        'order_id',
        'file_path',
        'created_by',
    ];

    public function order()
    {
        return $this->belongsTo(SalesOrder::class, 'order_id');
    }

    public function items()
    {
        return $this->hasMany(SalesPackingListItem::class, 'packing_list_id');
    }
}
