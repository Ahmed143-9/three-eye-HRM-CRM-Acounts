<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ErpSalarySheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErpSalarySheetController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage expense') || Auth::user()->type == 'company') {
            $workspace_id = Auth::user()->currentWorkspace ?? 1;
            $salarySheets = ErpSalarySheet::with('employee')
                ->where('workspace_id', $workspace_id)
                ->latest()
                ->get();
            return view('erp_salary_sheets.index', compact('salarySheets'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function generate(Request $request)
    {
        $month = $request->month ?? date('Y-m');
        $employees = Employee::where('created_by', Auth::user()->creatorId())->get();

        $count = 0;
        foreach ($employees as $employee) {
            $exists = ErpSalarySheet::where('employee_id', $employee->id)->where('month', $month)->exists();
            if (!$exists) {
                $netSalary = $employee->get_net_salary();
                ErpSalarySheet::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'net_salary' => $netSalary,
                    'deduction_amount' => 0,
                    'final_salary' => $netSalary,
                    'approval_status' => 'Pending',
                    'workspace_id' => Auth::user()->currentWorkspace ?? 1,
                    'created_by' => Auth::user()->id,
                ]);
                $count++;
            }
        }

        if ($count > 0) {
            $admins = \App\Models\User::where('type', 'company')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'salary_sheet_submitted',
                    'title' => 'New Salary Sheets Generated',
                    'message' => $count . ' salary sheets generated for ' . $month . ' and pending approval.',
                    'related_model' => 'ErpSalarySheet',
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            }
        }

        return redirect()->back()->with('success', __(':count salary sheets generated for :month.', ['count' => $count, 'month' => $month]));
    }

    public function destroy($id)
    {
        $sheet = ErpSalarySheet::find($id);
        if ($sheet) {
            $sheet->delete();
            return redirect()->back()->with('success', __('Salary sheet deleted.'));
        }
        return redirect()->back()->with('error', __('Not found.'));
    }

    public function approve(Request $request, $id)
    {
        if (Auth::user()->can('approve expense') || Auth::user()->type == 'company') {
            $sheet = ErpSalarySheet::find($id);
            if ($sheet) {
                $sheet->approval_status = 'Approved';
                $sheet->approved_by = Auth::user()->id;
                $sheet->save();

                // Notify Creator
                \App\Models\Notification::create([
                    'user_id' => $sheet->created_by,
                    'type' => 'salary_approved',
                    'title' => 'Salary Approved',
                    'message' => 'Salary sheet for ' . $sheet->month . ' has been approved.',
                    'related_model' => 'ErpSalarySheet',
                    'related_id' => $sheet->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);

                // Notify Accounts (Users who can manage bills)
                $accountants = \App\Models\User::whereHas('roles.permissions', function($q) {
                    $q->where('name', 'manage bill');
                })->orWhere('type', 'company')->pluck('id');

                foreach ($accountants as $accId) {
                    if ($accId == Auth::user()->id) continue;
                    \App\Models\Notification::create([
                        'user_id' => $accId,
                        'type' => 'salary_approved',
                        'title' => 'Salary Ready For Payment',
                        'message' => 'New approved salary sheet for ' . $sheet->month . ' is ready for payment.',
                        'related_model' => 'ErpSalarySheet',
                        'related_id' => $sheet->id,
                        'created_by' => Auth::user()->id,
                        'is_read' => 0,
                    ]);
                }

                return redirect()->back()->with('success', __('Salary sheet approved.'));
            }
            return redirect()->back()->with('error', __('Not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function reject(Request $request, $id)
    {
        if (Auth::user()->can('approve expense') || Auth::user()->type == 'company') {
            $sheet = ErpSalarySheet::find($id);
            if ($sheet) {
                $sheet->approval_status = 'Rejected';
                $sheet->save();

                \App\Models\Notification::create([
                    'user_id' => $sheet->created_by,
                    'type' => 'salary_rejected',
                    'title' => 'Salary Rejected',
                    'message' => 'Your salary sheet for ' . $sheet->month . ' has been rejected by Admin.',
                    'related_model' => 'ErpSalarySheet',
                    'related_id' => $sheet->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);

                return redirect()->back()->with('success', __('Salary sheet rejected.'));
            }
            return redirect()->back()->with('error', __('Not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }
}
