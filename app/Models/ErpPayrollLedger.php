<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErpPayrollLedger extends Model
{
    protected $fillable = [
        'batch_id',
        'salary_sheet_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'created_by',
    ];

    public function batch()
    {
        return $this->belongsTo(ErpPayrollBatch::class, 'batch_id');
    }
}
