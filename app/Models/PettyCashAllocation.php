<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PettyCashAllocation extends Model
{
    protected $fillable = [
        'month',
        'allocated_amount',
        'rollover_amount',
        'total_amount',
        'used_amount',
        'allocated_by'
    ];

    public function usages()
    {
        return $this->hasMany(PettyCashUsage::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }
}
