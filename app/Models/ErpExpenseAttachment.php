<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpExpenseAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'erp_expense_id',
        'file_path',
        'file_name',
    ];

    public function expense()
    {
        return $this->belongsTo(ErpExpense::class, 'erp_expense_id', 'id');
    }
}
