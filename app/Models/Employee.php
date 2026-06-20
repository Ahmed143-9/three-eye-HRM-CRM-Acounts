<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'dob',
        'gender',
        'phone',
        'address',
        'email',
        'password',
        'employee_id',
        'branch_id',
        'department_id',
        'designation_id',
        'company_doj',
        'documents',
        'account_holder_name',
        'account_number',
        'bank_name',
        'bank_identifier_code',
        'branch_location',
        'tax_payer_id',
        'salary_type',
        'biometric_emp_id',
        'account',
        'salary',
        'created_by',
        'profile_image',
        'facebook',
        'linkedin',
        'twitter',
        'instagram',
        'probation_period',
        'notice_period',
        'is_fresher',
        'joining_salary',
    ];

    public function documents()
    {
        return $this->hasMany('App\Models\EmployeeDocument', 'employee_id', 'employee_id');
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmployeeEmergencyContact::class, 'employee_id');
    }

    public function salary_type()
    {
        return $this->hasOne('App\Models\PayslipType', 'id', 'salary_type')->pluck('name')->first();
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function saturationDeductions()
    {
        return $this->hasMany(SaturationDeduction::class);
    }

    public function otherPayments()
    {
        return $this->hasMany(OtherPayment::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function get_net_salary()
{
    // Load related data efficiently using Eloquent relationships
    // $this->load('allowances', 'commissions', 'loans', 'saturationDeductions', 'otherPayments', 'overtimes');

    // Calculate total allowances
    $total_allowance = $this->allowances->sum(function ($allowance) {
        return ($allowance->type === 'fixed') ? $allowance->amount : ($allowance->amount * $this->salary / 100);
    });

    // Calculate total commissions
    $total_commission = $this->commissions->sum(function ($commission) {
        return ($commission->type === 'fixed') ? $commission->amount : ($commission->amount * $this->salary / 100);
    });

    // Calculate total loans
    $total_loan = $this->loans->sum(function ($loan) {
        return ($loan->type === 'fixed') ? $loan->amount : ($loan->amount * $this->salary / 100);
    });

    // Calculate total saturation deductions
    $total_saturation_deduction = $this->saturationDeductions->sum(function ($deduction) {
        return ($deduction->type === 'fixed') ? $deduction->amount : ($deduction->amount * $this->salary / 100);
    });

    // Calculate total other payments
    $total_other_payment = $this->otherPayments->sum(function ($otherPayment) {
        return ($otherPayment->type === 'fixed') ? $otherPayment->amount : ($otherPayment->amount * $this->salary / 100);
    });

    // Calculate total overtime
    $total_over_time = $this->overtimes->sum(function ($over_time) {
        return $over_time->number_of_days * $over_time->hours * $over_time->rate;
    });

    // Calculate net salary
    $net_salary = $this->salary + $total_allowance + $total_commission - $total_loan - $total_saturation_deduction + $total_other_payment + $total_over_time;

    return $net_salary;
}


    public static function allowance($id)
    {

        //allowance
        $allowances      = Allowance::where('employee_id', '=', $id)->get();
        $total_allowance = 0;
        foreach($allowances as $allowance)
        {
            $total_allowance = $allowance->amount + $total_allowance;
        }

        $allowance_json = json_encode($allowances);

        return $allowance_json;

    }

    public static function commission($id)
    {
        //commission
        $commissions      = Commission::where('employee_id', '=', $id)->get();
        $total_commission = 0;
        foreach($commissions as $commission)
        {
            $total_commission = $commission->amount + $total_commission;
        }
        $commission_json = json_encode($commissions);

        return $commission_json;

    }

    public static function loan($id)
    {
        //Loan
        $loans      = Loan::where('employee_id', '=', $id)->get();
        $total_loan = 0;
        foreach($loans as $loan)
        {
            $total_loan = $loan->amount + $total_loan;
        }
        $loan_json = json_encode($loans);

        return $loan_json;
    }

    public static function saturation_deduction($id)
    {
        //Saturation Deduction
        $saturation_deductions      = SaturationDeduction::where('employee_id', '=', $id)->get();
        $total_saturation_deduction = 0;
        foreach($saturation_deductions as $saturation_deduction)
        {
            $total_saturation_deduction = $saturation_deduction->amount + $total_saturation_deduction;
        }
        $saturation_deduction_json = json_encode($saturation_deductions);

        return $saturation_deduction_json;

    }

    public static function other_payment($id)
    {
        //OtherPayment
        $other_payments      = OtherPayment::where('employee_id', '=', $id)->get();
        $total_other_payment = 0;
        foreach($other_payments as $other_payment)
        {
            $total_other_payment = $other_payment->amount + $total_other_payment;
        }
        $other_payment_json = json_encode($other_payments);

        return $other_payment_json;
    }

    public static function overtime($id)
    {
        //Overtime
        $over_times      = Overtime::where('employee_id', '=', $id)->get();
        $total_over_time = 0;
        foreach($over_times as $over_time)
        {
            $total_work      = $over_time->number_of_days * $over_time->hours;
            $amount          = $total_work * $over_time->rate;
            $total_over_time = $amount + $total_over_time;
        }
        $over_time_json = json_encode($over_times);

        return $over_time_json;
    }

    public static function employee_id()
    {
        $employee = Employee::latest()->first();

        return !empty($employee) ? $employee->id + 1 : 1;
    }

    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'branch_id');
    }

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function designation()
    {
        return $this->hasOne('App\Models\Designation', 'id', 'designation_id');
    }

    public function salaryType()
    {
        return $this->hasOne('App\Models\PayslipType', 'id', 'salary_type');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function paySlip()
    {
        return $this->hasOne('App\Models\PaySlip', 'id', 'employee_id');
    }

    public function bankAccount()
    {
        return $this->hasOne('App\Models\BankAccount', 'id', 'account');
    }

    /**
     * Get all asset assignments for this employee
     */
    public function employeeAssets()
    {
        return $this->hasMany(EmployeeAsset::class, 'employee_id');
    }

    /**
     * Get currently assigned assets
     */
    public function currentAssets()
    {
        return $this->hasMany(EmployeeAsset::class, 'employee_id')
                    ->whereNull('return_date')
                    ->where('status', 'Assigned');
    }

    /**
     * Get asset requests made by this employee
     */
    public function assetRequests()
    {
        return $this->hasMany(AssetRequest::class, 'employee_id');
    }


    public function present_status($employee_id, $data)
    {
        return AttendanceEmployee::where('employee_id', $employee_id)->where('date', $data)->first();
    }


    public static function employee_salary($salary)
    {
        $employee = Employee::where("salary", $salary)->first();
        if ($employee->salary == '0' || $employee->salary == '0.0') {
            return "-";
        } else {
            return $employee->salary;
        }
    }

    public function getLeaveEntitlement($leaveTypeTitle, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        if (empty($this->company_doj)) {
            return 0;
        }
        $doj = \Carbon\Carbon::parse($this->company_doj);
        $dojYear = $doj->year;

        if ($dojYear > $year) {
            return 0;
        }

        $annualDays = ($leaveTypeTitle === 'Casual Leave') ? 10 : (($leaveTypeTitle === 'Sick Leave') ? 14 : 0);
        if ($annualDays === 0) {
            $leaveType = \App\Models\LeaveType::where('title', $leaveTypeTitle)->first();
            $annualDays = $leaveType ? $leaveType->days : 0;
        }

        if ($dojYear == $year) {
            // Pro-rate
            $startOfYear = \Carbon\Carbon::parse("$year-01-01");
            $endOfYear = \Carbon\Carbon::parse("$year-12-31");
            $totalDaysInYear = $startOfYear->diffInDays($endOfYear) + 1;
            $daysFromDojToEndOfYear = $doj->diffInDays($endOfYear) + 1;
            
            $entitlement = ($annualDays * $daysFromDojToEndOfYear) / $totalDaysInYear;
            return round($entitlement, 2);
        }

        // Joined before this year
        return $annualDays;
    }

    public function getApprovedLeaveDays($leaveTypeTitle, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $leaveType = \App\Models\LeaveType::where('title', $leaveTypeTitle)->first();
        if (!$leaveType) {
            return 0;
        }

        return \App\Models\Leave::where('employee_id', $this->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('status', 'Approved')
            ->where(function($q) use ($year) {
                $q->whereYear('start_date', $year)
                  ->orWhereYear('end_date', $year);
            })
            ->sum('total_leave_days');
    }

    public function getMonthlyAttendanceStats($month = null)
    {
        if (!$month) {
            $month = date('Y-m');
        }
        
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $attendances = \App\Models\AttendanceEmployee::where('employee_id', $this->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
            
        $pCount = 0;
        $aCount = 0;
        $lCount = 0;
        $lvCount = 0;
        
        foreach ($attendances as $att) {
            if ($att->status == 'Present') {
                $pCount++;
            } elseif ($att->status == 'Late') {
                $lCount++;
            } elseif ($att->status == 'Leave') {
                $lvCount++;
            } elseif ($att->status == 'Absent') {
                // Check if approved leave exists for this date
                $hasApprovedLeave = \App\Models\Leave::where('employee_id', $this->id)
                    ->where('status', 'Approved')
                    ->whereDate('start_date', '<=', $att->date)
                    ->whereDate('end_date', '>=', $att->date)
                    ->exists();
                    
                if ($hasApprovedLeave) {
                    $lvCount++;
                } else {
                    $aCount++;
                }
            }
        }
        
        return [
            'present' => $pCount,
            'absent' => $aCount,
            'late' => $lCount,
            'leave' => $lvCount,
        ];
    }
}
