<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\ErpSalarySheet;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErpSalarySheetController extends Controller
{
    /** ────────────────────────────────────────────────────────────
     *  INDEX — Spreadsheet listing grouped by month
     * ─────────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        if (!Auth::user()->can('manage expense') && Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $month       = $request->input('month', date('Y-m'));
        $workspaceId = Auth::user()->currentWorkspace ?? 1;

        $salarySheets = ErpSalarySheet::with(['employee', 'department', 'designation', 'approvedBy', 'paidBy'])
            ->where('workspace_id', $workspaceId)
            ->where('salary_month', $month)
            ->orderBy('id')
            ->get();

        $departments  = Department::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $designations = Designation::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');

        $isAdmin = (Auth::user()->type === 'company' || Auth::user()->can('approve expense'));
        $isHRM   = Auth::user()->can('manage employee');
        $isAccounts = Auth::user()->can('manage bill');

        return view('erp_salary_sheets.index', compact('salarySheets', 'month', 'departments', 'designations', 'isAdmin', 'isHRM', 'isAccounts'));
    }

    /** ────────────────────────────────────────────────────────────
     *  GENERATE — Create salary sheets with auto attendance data
     * ─────────────────────────────────────────────────────────── */
    public function generate(Request $request)
    {
        $month       = $request->input('month', date('Y-m'));
        $workspaceId = Auth::user()->currentWorkspace ?? 1;

        $startDate        = $month . '-01';
        $endDate          = date('Y-m-t', strtotime($startDate));
        $totalDaysInMonth = (int) date('t', strtotime($startDate));

        $employees = Employee::where('created_by', Auth::user()->creatorId())->get();
        $count     = 0;

        foreach ($employees as $employee) {
            if (ErpSalarySheet::where('employee_id', $employee->id)->where('salary_month', $month)->exists()) {
                continue;
            }

            $attendances = AttendanceEmployee::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $presentDays = $attendances->filter(fn($a) => strtolower($a->status) === 'present')->count();
            $absentDays  = $attendances->filter(fn($a) => strtolower($a->status) === 'absent')->count();
            $lateCount   = $attendances->where('late', 1)->count();
            $leaveCount  = $attendances->filter(fn($a) => strtolower($a->status) === 'leave')->count();

            $totalWorkHours   = 0;
            $totalOvertimeHrs = 0;

            foreach ($attendances as $att) {
                if ($att->clock_in && $att->clock_out) {
                    $hours = (strtotime($att->clock_out) - strtotime($att->clock_in)) / 3600;
                    if ($hours > 0) {
                        $totalWorkHours += $hours;
                        if ($hours > 8) $totalOvertimeHrs += ($hours - 8);
                    }
                }
            }

            $dailySalary   = $employee->salary > 0 ? ($employee->salary / $totalDaysInMonth) : 0;
            $payableAmount = round($dailySalary * $presentDays, 2);
            $netSalary     = (float)$employee->get_net_salary();

            $serialNo = 'SAL-' . strtoupper(str_replace('-', '', $month)) . '-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT);

            ErpSalarySheet::create([
                'serial_no'        => $serialNo,
                'status'           => 'Draft',
                'employee_id'      => $employee->id,
                'department_id'    => $employee->department_id,
                'designation_id'   => $employee->designation_id,
                'salary_month'     => $month,
                'present_days'     => $presentDays,
                'absent_days'      => $absentDays,
                'late_count'       => $lateCount,
                'leave_count'      => $leaveCount,
                'working_hours'    => round($totalWorkHours, 2),
                'overtime_hours'   => round($totalOvertimeHrs, 2),
                'payable_amount'   => $payableAmount,
                'receivable_amount'=> 0,
                'net_salary'       => $netSalary,
                'deduction_amount' => 0,
                'final_salary'     => $netSalary + $payableAmount, // Initial formula
                'approval_status'  => 'Pending',
                'payment_status'   => 'Unpaid',
                'workspace_id'     => $workspaceId,
                'created_by'       => Auth::user()->id,
            ]);
            $count++;
        }

        return redirect()->back()->with('success', __(':count salary sheets generated as Draft.', ['count' => $count]));
    }

    /** ────────────────────────────────────────────────────────────
     *  SUBMIT FOR APPROVAL — HRM Action
     * ─────────────────────────────────────────────────────────── */
    public function submitForApproval(Request $request, $id)
    {
        $sheet = ErpSalarySheet::find($id);
        if (!$sheet || $sheet->status !== 'Draft') {
            return redirect()->back()->with('error', __('Invalid request or already submitted.'));
        }

        $sheet->update([
            'status'           => 'Pending Approval',
            'need_approval_at' => now(),
        ]);

        // Notify Admin
        $admins = User::where('type', 'company')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'       => $admin->id,
                'type'          => 'salary_submitted',
                'title'         => 'Salary Sheet Pending Approval',
                'message'       => 'Salary sheet for ' . optional($sheet->employee)->name . ' is pending your review.',
                'related_model' => 'ErpSalarySheet',
                'related_id'    => $sheet->id,
                'created_by'    => Auth::user()->id,
                'is_read'       => 0,
            ]);
        }

        return redirect()->back()->with('success', __('Salary sheet submitted for Admin approval.'));
    }

    /** ────────────────────────────────────────────────────────────
     *  UPDATE ROW — Admin inline edit (formula applied)
     * ─────────────────────────────────────────────────────────── */
    public function updateRow(Request $request, $id)
    {
        $isAdmin = (Auth::user()->type === 'company' || Auth::user()->can('approve expense'));
        if (!$isAdmin) {
            return response()->json(['success' => false, 'message' => __('Permission denied.')], 403);
        }

        $sheet = ErpSalarySheet::find($id);
        if (!$sheet) return response()->json(['success' => false, 'message' => __('Record not found.')], 404);

        $deduction  = (float) $request->input('deduction_amount', $sheet->deduction_amount);
        $receivable = (float) $request->input('receivable_amount', $sheet->receivable_amount);
        $payable    = (float) $request->input('payable_amount', $sheet->payable_amount);
        $cause      = $request->input('cause_of_deduction', $sheet->cause_of_deduction);
        $remarks    = $request->input('remarks', $sheet->remarks);

        // ERP Formula: Final = (Net + Payable) - (Deduction + Receivable)
        $finalSalary = ($sheet->net_salary + $payable) - ($deduction + $receivable);

        $sheet->update([
            'deduction_amount'   => $deduction,
            'receivable_amount'  => $receivable,
            'payable_amount'     => $payable,
            'cause_of_deduction' => $cause,
            'final_salary'       => $finalSalary,
            'remarks'            => $remarks,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => __('Data updated.'),
            'final_salary' => $finalSalary,
        ]);
    }

    /** ────────────────────────────────────────────────────────────
     *  APPROVE — Admin Action
     * ─────────────────────────────────────────────────────────── */
    public function approve(Request $request, $id)
    {
        $isAdmin = (Auth::user()->type === 'company' || Auth::user()->can('approve expense'));
        if (!$isAdmin) return redirect()->back()->with('error', __('Permission denied.'));

        $sheet = ErpSalarySheet::find($id);
        if ($sheet) {
            $sheet->update([
                'status'          => 'Approved',
                'approval_status' => 'Approved',
                'approved_by'     => Auth::user()->id,
                'approved_at'     => now(),
            ]);

            // Notify Accounts
            $accountants = User::whereHas('roles.permissions', function ($q) {
                $q->where('name', 'manage bill');
            })->orWhere('type', 'company')->get();

            foreach ($accountants as $acc) {
                Notification::create([
                    'user_id'       => $acc->id,
                    'type'          => 'salary_approved',
                    'title'         => 'Salary Approved - Ready for Payment',
                    'message'       => 'Salary for ' . optional($sheet->employee)->name . ' approved. Final: ' . number_format($sheet->final_salary, 2),
                    'related_model' => 'ErpSalarySheet',
                    'related_id'    => $sheet->id,
                    'created_by'    => Auth::user()->id,
                    'is_read'       => 0,
                ]);
            }

            return redirect()->back()->with('success', __('Salary sheet approved and moved to Accounting.'));
        }
        return redirect()->back()->with('error', __('Not found.'));
    }

    /** ────────────────────────────────────────────────────────────
     *  MARK AS PAID — Accounts Action
     * ─────────────────────────────────────────────────────────── */
    public function markAsPaid(Request $request, $id)
    {
        $canPay = (Auth::user()->can('manage bill') || Auth::user()->type === 'company');
        if (!$canPay) return redirect()->back()->with('error', __('Permission denied.'));

        $sheet = ErpSalarySheet::find($id);
        if ($sheet && $sheet->status === 'Approved') {
            $sheet->update([
                'status'         => 'Paid',
                'payment_status' => 'Paid',
                'paid_by'        => Auth::user()->id,
                'paid_at'        => now(),
            ]);

            // Create Ledger Transaction
            Transaction::create([
                'account'     => 0, // General or specific payroll account if exists
                'type'        => 'Expense',
                'amount'      => $sheet->final_salary,
                'description' => 'Salary Payment: ' . optional($sheet->employee)->name . ' (' . $sheet->salary_month . ')',
                'date'        => date('Y-m-d'),
                'created_by'  => Auth::user()->id,
                'payment_id'  => $sheet->id,
            ]);

            // Notify HRM + Admin
            $notifUsers = User::whereIn('id', [$sheet->created_by, $sheet->approved_by])->get();
            foreach ($notifUsers as $nu) {
                Notification::create([
                    'user_id'       => $nu->id,
                    'type'          => 'salary_paid',
                    'title'         => 'Salary Payment Confirmed',
                    'message'       => 'Payment of ' . number_format($sheet->final_salary, 2) . ' for ' . optional($sheet->employee)->name . ' is complete.',
                    'related_model' => 'ErpSalarySheet',
                    'related_id'    => $sheet->id,
                    'created_by'    => Auth::user()->id,
                    'is_read'       => 0,
                ]);
            }

            return redirect()->back()->with('success', __('Salary marked as Paid and ledger updated.'));
        }
        return redirect()->back()->with('error', __('Only approved sheets can be paid.'));
    }

    /** ────────────────────────────────────────────────────────────
     *  ACCOUNTING APPROVED BILLS — Dedicated View
     * ─────────────────────────────────────────────────────────── */
    public function accountingApprovedBills(Request $request)
    {
        if (!Auth::user()->can('manage bill') && Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $month = $request->input('month', date('Y-m'));
        $workspaceId = Auth::user()->currentWorkspace ?? 1;

        $approvedSheets = ErpSalarySheet::with(['employee', 'department', 'designation', 'approvedBy'])
            ->where('workspace_id', $workspaceId)
            ->where('salary_month', $month)
            ->where('status', 'Approved')
            ->get();

        return view('accounting.approved_bills', compact('approvedSheets', 'month'));
    }

    public function destroy($id)
    {
        $sheet = ErpSalarySheet::find($id);
        if ($sheet && in_array($sheet->status, ['Draft', 'Rejected'])) {
            $sheet->delete();
            return redirect()->back()->with('success', __('Salary sheet deleted.'));
        }
        return redirect()->back()->with('error', __('Only Draft or Rejected sheets can be deleted.'));
    }

}
