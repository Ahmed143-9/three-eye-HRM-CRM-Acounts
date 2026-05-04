<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePerformance extends Model
{
    use HasFactory;

    protected $table = 'employee_performances';

    protected $fillable = [
        'employee_id',
        'performance_month',
        'department_id',
        'designation_id',
        'present_days',
        'absent_days',
        'late_count',
        'leave_count',
        'total_working_hours',
        'overtime_hours',
        'payable_amount',
        'receivable_amount',
        'notes',
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
}
