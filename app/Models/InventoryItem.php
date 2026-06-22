<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'type', 'unit', 'created_by'];

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class, 'inventory_item_id');
    }

    public function usages()
    {
        return $this->hasMany(InventoryUsage::class, 'inventory_item_id');
    }
}
