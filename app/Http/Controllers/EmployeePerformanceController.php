<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeePerformance;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePerformanceController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('manage expense') && Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $month = $request->input('month', date('Y-m'));
        $workspaceId = Auth::user()->currentWorkspace ?? 1;

        $performances = EmployeePerformance::with(['employee.department', 'employee.designation', 'department', 'designation'])
            ->where('workspace_id', $workspaceId)
            ->where('performance_month', $month)
            ->get();

        $departments  = Department::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $designations = Designation::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');

        return view('employee_performance.index', compact('performances', 'month', 'departments', 'designations'));
    }

    public function generate(Request $request)
    {
        if (!Auth::user()->can('manage expense') && Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $month = $request->input('month', date('Y-m'));
        $workspaceId = Auth::user()->currentWorkspace ?? 1;

        // Parse month to get start/end dates
        $startDate = $month . '-01';
        $endDate   = date('Y-m-t', strtotime($startDate));
        $totalDaysInMonth = (int) date('t', strtotime($startDate));

        $employees = Employee::where('created_by', Auth::user()->creatorId())->get();
        $count = 0;

        foreach ($employees as $employee) {
            // Get all attendance for this employee in the month
            $attendances = AttendanceEmployee::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $presentDays  = $attendances->whereIn('status', ['Present', 'present'])->count();
            $absentDays   = $attendances->whereIn('status', ['Absent', 'absent'])->count();
            $lateCount    = $attendances->where('late', 1)->count();
            $leaveCount   = $attendances->whereIn('status', ['Leave', 'leave'])->count();

            // Working hours: sum of (clock_out - clock_in) in hours
            $totalWorkingHours = 0;
            $totalOvertimeHours = 0;
            foreach ($attendances as $att) {
                if ($att->clock_in && $att->clock_out) {
                    $in  = strtotime($att->clock_in);
                    $out = strtotime($att->clock_out);
                    if ($out > $in) {
                        $hours = ($out - $in) / 3600;
                        $totalWorkingHours += $hours;
                        // Overtime: anything over 8 hours per day
                        if ($hours > 8) {
                            $totalOvertimeHours += ($hours - 8);
                        }
                    }
                }
                // Also use the overtime column if set
                if ($att->overtime) {
                    // overtime stored as hours in DB
                    $totalOvertimeHours = max($totalOvertimeHours, 0);
                }
            }

            // Calculate payable (based on present days)
            $dailySalary  = $employee->salary > 0 ? ($employee->salary / $totalDaysInMonth) : 0;
            $payableAmount = round($dailySalary * $presentDays, 2);
            $receivableAmount = 0; // Could be allowances etc., set to 0 for now

            // Upsert performance record
            EmployeePerformance::updateOrCreate(
                ['employee_id' => $employee->id, 'performance_month' => $month],
                [
                    'department_id'      => $employee->department_id,
                    'designation_id'     => $employee->designation_id,
                    'present_days'       => $presentDays,
                    'absent_days'        => $absentDays,
                    'late_count'         => $lateCount,
                    'leave_count'        => $leaveCount,
                    'total_working_hours'=> round($totalWorkingHours, 2),
                    'overtime_hours'     => round($totalOvertimeHours, 2),
                    'payable_amount'     => $payableAmount,
                    'receivable_amount'  => $receivableAmount,
                    'workspace_id'       => $workspaceId,
                    'created_by'         => Auth::user()->id,
                ]
            );
            $count++;
        }

        return redirect()->back()->with('success', __(':count performance records generated for :month.', [
            'count' => $count,
            'month' => $month,
        ]));
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('manage expense') && Auth::user()->type !== 'company') {
            return response()->json(['success' => false, 'message' => __('Permission denied.')], 403);
        }

        $performance = EmployeePerformance::find($id);
        if (!$performance) return response()->json(['success' => false, 'message' => __('Not found.')], 404);

        $performance->update($request->all());

        return response()->json(['success' => true, 'message' => __('Performance updated.')]);
    }
}

