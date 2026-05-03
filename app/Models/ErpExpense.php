<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpExpense extends Model
{
    use HasFactory;

    protected $table = 'erp_expenses';

    protected $fillable = [
        'serial_no',
        'type',
        'erp_expense_category_id',
        'date',
        'billing_month',
        'description',
        'amount',
        'status',
        'employee_id',
        'department_id',
        'designation_id',
        'supplier_id',
        'transport_id',
        'trip_no',
        'net_salary',
        'deduction_amount',
        'cause_of_deduction',
        'attachment',
        'remarks',
        'approved_by',
        'approved_at',
        'accounting_bill_id',
        'accountant_note',
        'payment_status',
        'is_paid',
        'erp_salary_sheet_id',
        'workspace_id',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ErpExpenseCategory::class, 'erp_expense_category_id', 'id');
    }

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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function items()
    {
        return $this->hasMany(ErpExpenseItem::class, 'erp_expense_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(ErpExpenseAttachment::class, 'erp_expense_id', 'id');
    }

    public function statusLogs()
    {
        return $this->hasMany(ErpExpenseStatusLog::class, 'erp_expense_id', 'id')->orderBy('created_at', 'desc');
    }
}
