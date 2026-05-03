<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'module_type',
        'is_active',
        'workspace_id',
        'created_by',
    ];

    public function expenses()
    {
        return $this->hasMany(ErpExpense::class, 'erp_expense_category_id', 'id');
    }
}
