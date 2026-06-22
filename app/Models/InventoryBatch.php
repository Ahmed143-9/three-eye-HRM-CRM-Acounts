<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'supplier_id',
        'purchase_date',
        'quantity_purchased',
        'quantity_available',
        'unit_cost',
        'total_cost',
        'created_by'
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
