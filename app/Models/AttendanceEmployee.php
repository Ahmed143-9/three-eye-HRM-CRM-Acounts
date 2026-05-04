<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceEmployee extends Model
{
    protected $fillable = [
        'employee_id',
        'department_id',
        'date',
        'status',
        'clock_in',
        'clock_out',
        'working_hours',
        'late',
        'early_leaving',
        'overtime',
        'total_rest',
        'created_by',
        'is_late_update',
        'late_update_count',
    ];

    public function employees()
    {
        return $this->hasOne('App\Models\Employee', 'user_id', 'employee_id');
    }

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }
}
