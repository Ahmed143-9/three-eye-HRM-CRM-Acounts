<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpSalarySheet extends Model
{
    use HasFactory;

    protected $table = 'erp_salary_sheets';

    protected $fillable = [
        'employee_id',
        'month',
        'net_salary',
        'deduction_amount',
        'final_salary',
        'approval_status',
        'approved_by',
        'workspace_id',
        'created_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
