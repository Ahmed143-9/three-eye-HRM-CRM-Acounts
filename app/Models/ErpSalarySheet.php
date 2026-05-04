<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpSalarySheet extends Model
{
    use HasFactory;

    protected $table = 'erp_salary_sheets';

    protected $fillable = [
        'serial_no',
        'status',
        'batch_id',
        'employee_id',
        'department_id',
        'designation_id',
        'month',
        'net_salary',
        'basic_salary',
        'hra',
        'conveyance_allowance',
        'medical_allowance',
        'present_days',
        'absent_days',
        'late_count',
        'leave_count',
        'working_hours',
        'overtime_hours',
        'payable_amount',
        'receivable_amount',
        'net_salary',
        'basic_salary',
        'hra',
        'conveyance_allowance',
        'medical_allowance',
        'pf_contribution',
        'professional_tax',
        'tds',
        'salary_advance',
        'deduction_amount',
        'cause_of_deduction',
        'final_salary',
        'approval_status',
        'payment_status',
        'remarks',
        'need_approval_at',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'workspace_id',
        'created_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by', 'id');
    }
}

