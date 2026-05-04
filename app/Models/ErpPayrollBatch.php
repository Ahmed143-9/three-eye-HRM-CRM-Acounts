<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErpPayrollBatch extends Model
{
    protected $fillable = [
        'batch_no',
        'month',
        'department_id',
        'status',
        'total_net_payable',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    public function salarySheets()
    {
        return $this->hasMany(ErpSalarySheet::class, 'batch_id', 'id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }
}
