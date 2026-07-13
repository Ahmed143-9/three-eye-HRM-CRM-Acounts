<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\ErpSalarySheet;
use App\Models\ErpPayrollBatch;
use App\Models\ErpPayrollLedger;
use App\Models\Notification;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErpSalarySheetController extends Controller
{
    public function index(Request $request)
    {
        $batches = ErpPayrollBatch::where('created_by', Auth::user()->creatorId())
            ->with('department')
            ->orderBy('id', 'desc')
            ->get();

        return view('erp_salary_sheets.index', compact('batches'));
    }

    public function create()
    {
        $departments = Department::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        return view('erp_salary_sheets.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $month = $request->month;
        $deptId = $request->department_id;

        // Check if batch already exists for this month and dept
        $existing = ErpPayrollBatch::where('month', $month)->where('department_id', $deptId)->exists();
        if($existing) {
            return redirect()->back()->with('error', __('Payroll batch for this month/department already exists.'));
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            
            $batch = ErpPayrollBatch::create([
                'batch_no'      => 'PB-' . date('Ymd-His'),
                'month'         => $month,
                'department_id' => $deptId,
                'status'        => 'Draft',
                'created_by'    => Auth::user()->creatorId(),
            ]);

            $employeesQuery = Employee::where('created_by', Auth::user()->creatorId());
            if($deptId) {
                $employeesQuery->where('department_id', $deptId);
            }
            $employees = $employeesQuery->get();

            $startDate = $month . '-01';
            $endDate   = date('Y-m-t', strtotime($startDate));
            $daysInMonth = (int)date('t', strtotime($startDate));

            $totalBatchPayable = 0;

            // Fetch allowance options to lookup custom values
            $allowanceOptions = \App\Models\AllowanceOption::where('created_by', Auth::user()->creatorId())->get();
            $hraOpt = $allowanceOptions->where('name', 'House Rent Allowance')->first();
            $convOpt = $allowanceOptions->where('name', 'Conveyance Allowance')->first();
            if (!$convOpt) {
                $convOpt = $allowanceOptions->where('name', 'Transport Allowance')->first();
            }
            $medOpt = $allowanceOptions->where('name', 'Medical Allowance')->first();

            foreach($employees as $employee) {
                $attendances = AttendanceEmployee::where('employee_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $pCount  = 0;
                $aCount  = 0;
                $lCount  = 0;
                $lvCount = 0;
                $hrs     = $attendances->sum('working_hours');

                foreach ($attendances as $att) {
                    if ($att->status == 'Present') {
                        $pCount++;
                    } elseif ($att->status == 'Late') {
                        $lCount++;
                    } elseif ($att->status == 'Leave') {
                        $lvCount++;
                    } elseif ($att->status == 'Absent') {
                        // Check if approved leave exists for this date
                        $hasApprovedLeave = \App\Models\Leave::where('employee_id', $employee->id)
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

                $ctc = (float)($employee->joining_salary ?? 0);

                // Fetch custom allowance amounts if saved, else fallback to standard percentages
                $hraVal = $hraOpt ? \App\Models\Allowance::where('employee_id', $employee->id)->where('allowance_option', $hraOpt->id)->value('amount') : null;
                $convVal = $convOpt ? \App\Models\Allowance::where('employee_id', $employee->id)->where('allowance_option', $convOpt->id)->value('amount') : null;
                $medVal = $medOpt ? \App\Models\Allowance::where('employee_id', $employee->id)->where('allowance_option', $medOpt->id)->value('amount') : null;

                $basic = $employee->salary > 0 ? (float)$employee->salary : round($ctc * 0.60, 2);
                $hra   = !is_null($hraVal) ? (float)$hraVal : round($ctc * 0.20, 2);
                $conv  = !is_null($convVal) ? (float)$convVal : round($ctc * 0.10, 2);
                $med   = !is_null($medVal) ? (float)$medVal : round($ctc * 0.10, 2);

                $dailyPay = $daysInMonth > 0 ? ($ctc / $daysInMonth) : 0;
                
                // Late attendance policy: 3 late marks = 1 absent day
                $lateDeductions = (int)floor($lCount / 3);
                $payableDays = $pCount + $lvCount + $lCount - $lateDeductions;
                if ($payableDays > $daysInMonth) {
                    $payableDays = $daysInMonth;
                }
                if ($payableDays < 0) {
                    $payableDays = 0;
                }
                
                $payable = round($dailyPay * $payableDays, 2);

                ErpSalarySheet::create([
                    'batch_id'             => $batch->id,
                    'employee_id'          => $employee->id,
                    'department_id'        => $employee->department_id,
                    'designation_id'       => $employee->designation_id,
                    'month'                => $month,
                    'status'               => 'Draft',
                    'net_salary'           => $ctc,
                    'basic_salary'         => $basic,
                    'hra'                  => $hra,
                    'conveyance_allowance' => $conv,
                    'medical_allowance'    => $med,
                    'present_days'         => $pCount,
                    'absent_days'          => $aCount + $lateDeductions, // Actual Absences + Late Converted Absences
                    'late_count'           => $lCount,
                    'leave_count'          => $lvCount,
                    'working_hours'        => round($hrs, 2),
                    'payable_amount'       => $payable,
                    'final_salary'         => $payable, // Initially equal to payable
                    'created_by'           => Auth::user()->creatorId(),
                    'workspace_id'         => Auth::user()->currentWorkspace ?? 1,
                ]);

                $totalBatchPayable += $payable;
            }

            $batch->update(['total_net_payable' => $totalBatchPayable]);
            
            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('salary-management.show', $batch->id)->with('success', __('Payroll batch generated with :count records.', ['count' => count($employees)]));
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', __('Failed to generate salary sheet: ') . $e->getMessage());
        }
    }

    public function show($id)
    {
        $batch = ErpPayrollBatch::with(['salarySheets.employee', 'salarySheets.designation', 'creator'])->findOrFail($id);
        $isAdmin = (Auth::user()->type == 'company' || Auth::user()->can('Approve Expenses'));
        
        return view('erp_salary_sheets.show', compact('batch', 'isAdmin'));
    }

    public function submitForApproval($id)
    {
        $batch = ErpPayrollBatch::findOrFail($id);
        $batch->update([
            'status'      => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        // Bypassed Pending Approval queue as requested
        return redirect()->back()->with('success', __('Payroll batch finalized and instantly approved.'));
    }

    public function approve($id)
    {
        $batch = ErpPayrollBatch::findOrFail($id);
        $batch->update([
            'status'      => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->back()->with('success', __('Payroll batch approved.'));
    }

    public function pay(Request $request, $id)
    {
        $batch = ErpPayrollBatch::findOrFail($id);
        $batch->update(['status' => 'Paid']);

        ErpPayrollLedger::create([
            'batch_id'       => $batch->id,
            'amount'         => $batch->total_net_payable,
            'payment_date'   => date('Y-m-d'),
            'payment_method' => 'Bank Transfer',
            'created_by'     => Auth::id(),
        ]);

        // Create accounting transaction
        Transaction::create([
            'account'     => 0,
            'type'        => 'Expense',
            'amount'      => $batch->total_net_payable,
            'description' => 'Salary Payment - Batch ' . $batch->batch_no,
            'date'        => date('Y-m-d'),
            'created_by'  => Auth::id(),
        ]);

        return redirect()->back()->with('success', __('Payroll batch marked as Paid and ledger updated.'));
    }

    public function updateRow(Request $request, $id)
    {
        $row = ErpSalarySheet::findOrFail($id);
        
        $pf      = (float)$request->pf_contribution;
        $tax     = (float)$request->professional_tax;
        $tds     = (float)$request->tds;
        $advance = (float)$request->salary_advance;
        $manual  = (float)$request->deduction_amount;

        $finalSalary = $row->payable_amount - ($pf + $tax + $tds + $advance + $manual);

        $row->update([
            'pf_contribution'  => $pf,
            'professional_tax' => $tax,
            'tds'              => $tds,
            'salary_advance'   => $advance,
            'deduction_amount' => $manual,
            'final_salary'     => $finalSalary,
        ]);

        // Update Batch Total
        $batch = ErpPayrollBatch::find($row->batch_id);
        $batch->update(['total_net_payable' => ErpSalarySheet::where('batch_id', $batch->id)->sum('final_salary')]);

        return response()->json([
            'success'      => true,
            'message'      => __('Row updated.'),
            'final_salary' => $finalSalary,
        ]);
    }

    public function expenseApprovalQueue(Request $request)
    {
        if (!Auth::user()->can('Approve Expenses') && Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $batches = ErpPayrollBatch::where('status', 'Pending Approval')
            ->where('created_by', Auth::user()->creatorId())
            ->with('department')
            ->orderBy('id', 'desc')
            ->get();

        return view('erp_salary_sheets.index', compact('batches'));
    }

    public function approvedBills(Request $request)
    {
        if (!Auth::user()->can('Manage Purchases & Suppliers') && Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $batches = ErpPayrollBatch::where('status', 'Approved')
            ->where('created_by', Auth::user()->creatorId())
            ->with('department')
            ->orderBy('id', 'desc')
            ->get();

        return view('erp_salary_sheets.index', compact('batches'));
    }

    public function destroy($id)
    {
        $batch = ErpPayrollBatch::findOrFail($id);
        if($batch->status == 'Draft') {
            ErpSalarySheet::where('batch_id', $id)->delete();
            $batch->delete();
            return redirect()->back()->with('success', __('Batch deleted.'));
        }
        return redirect()->back()->with('error', __('Only draft batches can be deleted.'));
    }
}
