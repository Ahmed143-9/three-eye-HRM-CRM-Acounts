<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpExpenseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'erp_expense_id',
        'product_name',
        'quantity',
        'unit_id',
        'unit_price',
        'amount',
    ];

    public function expense()
    {
        return $this->belongsTo(ErpExpense::class, 'erp_expense_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(ErpExpenseUnit::class, 'unit_id', 'id');
    }
}
