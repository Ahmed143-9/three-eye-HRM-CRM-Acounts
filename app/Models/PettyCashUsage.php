<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PettyCashUsage extends Model
{
    protected $fillable = [
        'petty_cash_allocation_id',
        'date',
        'amount',
        'purpose',
        'user_id'
    ];

    public function allocation()
    {
        return $this->belongsTo(PettyCashAllocation::class, 'petty_cash_allocation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
