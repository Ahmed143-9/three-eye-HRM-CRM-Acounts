<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingPayment extends Model
{
    protected $fillable = [
        'billable_type',
        'billable_id',
        'amount',
        'adjustment_amount',
        'adjustment_reason',
        'date',
        'payment_method',
        'note',
        'next_due_date',
        'created_by',
    ];

    public function billable()
    {
        return $this->morphTo();
    }
}
