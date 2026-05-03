<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpExpenseStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'erp_expense_id',
        'status',
        'comments',
        'user_id',
    ];

    public function expense()
    {
        return $this->belongsTo(ErpExpense::class, 'erp_expense_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
