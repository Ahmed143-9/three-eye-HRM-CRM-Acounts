<?php

namespace App\Http\Controllers;

use App\Imports\AttendanceImport;
use App\Models\AttendanceEmployee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\IpRestrict;
use App\Models\Notification;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceEmployeeController extends Controller
{
    public function history(Request $request)
    {
        if (\Auth::user()->can('manage attendance')) {
            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All Departments', '');

            $attendanceQuery = AttendanceEmployee::where('created_by', \Auth::user()->creatorId());

            if (!empty($request->month)) {
                $attendanceQuery->where('date', 'like', $request->month . '%');
            }

            if (!empty($request->department)) {
                $attendanceQuery->whereHas('employee', function ($q) use ($request) {
                    $q->where('department_id', $request->department);
                });
            }

            if (!empty($request->employee)) {
                $attendanceQuery->where('employee_id', $request->employee);
            }

            $attendances = $attendanceQuery->with('employee.department', 'employee.designation')->orderBy('date', 'desc')->get();

            return view('attendance.history', compact('attendances', 'department'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage attendance')) {

            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('Select Branch', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('Select Department', '');

            if (\Auth::user()->type == 'Employee' || \Auth::user()->type == 'client') {

                $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;

                $attendanceEmployee = AttendanceEmployee::where('employee_id', $emp);

                if ($request->type == 'monthly' && !empty($request->month)) {
                    $month = date('m', strtotime($request->month));
                    $year = date('Y', strtotime($request->month));

                    $start_date = date($year . '-' . $month . '-01');
                    $end_date = date($year . '-' . $month . '-t');

                    $attendanceEmployee->whereBetween(
                        'date',
                        [
                            $start_date,
                            $end_date,
                        ]
                    );
                } elseif ($request->type == 'daily' && !empty($request->date)) {
                    $attendanceEmployee->where('date', $request->date);
                } else {
                    $month = date('m');
                    $year = date('Y');
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date = date($year . '-' . $month . '-t');

                    $attendanceEmployee->whereBetween(
                        'date',
                        [
                            $start_date,
                            $end_date,
                        ]
                    );
                }
                $attendanceEmployee = $attendanceEmployee->get();

            } else {

                $employee = Employee::select('id')->where('created_by', \Auth::user()->creatorId());
                if (!empty($request->branch)) {
                    $employee->where('branch_id', $request->branch);
                }

                if (!empty($request->department)) {
                    $employee->where('department_id', $request->department);
                }

                $employee = $employee->get()->pluck('id');

                $attendanceEmployee = AttendanceEmployee::whereIn('employee_id', $employee);
                if ($request->type == 'monthly' && !empty($request->month)) {

                    $month = date('m', strtotime($request->month));
                    $year = date('Y', strtotime($request->month));
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date = date('Y-m-t', strtotime('01-' . $month . '-' . $year));


                    $attendanceEmployee->whereBetween(
                        'date',
                        [
                            $start_date,
                            $end_date,
                        ]
                    );
                } elseif ($request->type == 'daily' && !empty($request->date)) {
                    $attendanceEmployee->where('date', $request->date);
                } else {

                    $month = date('m');
                    $year = date('Y');
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date = date('Y-m-t', strtotime('01-' . $month . '-' . $year));


                    $attendanceEmployee->whereBetween(
                        'date',
                        [
                            $start_date,
                            $end_date,
                        ]
                    );
                }

                $attendanceEmployee = $attendanceEmployee->get();
            }

            return view('attendance.index', compact('attendanceEmployee', 'branch', 'department'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create attendance')) {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('attendance.create', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create attendance')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required',
                    'date' => 'required',
                    'clock_in' => 'required',
                    'clock_out' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $startTime = Utility::getValByName('company_start_time');
            $endTime = Utility::getValByName('company_end_time');
            $attendance = AttendanceEmployee::where('employee_id', '=', $request->employee_id)->where('date', '=', $request->date)->where('clock_out', '=', '00:00:00')->get()->toArray();
            if ($attendance) {
                return redirect()->route('attendanceemployee.index')->with('error', __('Employee Attendance Already Created.'));
            } else {
                $date = date("Y-m-d");

                $totalLateSeconds = strtotime($request->clock_in) - strtotime($date . $startTime);

                $hours = floor($totalLateSeconds / 3600);
                $mins = floor($totalLateSeconds / 60 % 60);
                $secs = floor($totalLateSeconds % 60);

                $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                //early Leaving
                $totalEarlyLeavingSeconds = strtotime($date . $endTime) - strtotime($request->clock_out);
                $hours = floor($totalEarlyLeavingSeconds / 3600);
                $mins = floor($totalEarlyLeavingSeconds / 60 % 60);
                $secs = floor($totalEarlyLeavingSeconds % 60);
                $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                if (strtotime($request->clock_out) > strtotime($date . $endTime)) {
                    //Overtime
                    $totalOvertimeSeconds = strtotime($request->clock_out) - strtotime($date . $endTime);
                    $hours = floor($totalOvertimeSeconds / 3600);
                    $mins = floor($totalOvertimeSeconds / 60 % 60);
                    $secs = floor($totalOvertimeSeconds % 60);
                    $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                } else {
                    $overtime = '00:00:00';
                }

                $employeeAttendance = new AttendanceEmployee();
                $employeeAttendance->employee_id = $request->employee_id;
                $employeeAttendance->date = $request->date;
                $employeeAttendance->status = 'Present';
                $employeeAttendance->clock_in = $request->clock_in . ':00';
                $employeeAttendance->clock_out = $request->clock_out . ':00';
                $employeeAttendance->late = $late;
                $employeeAttendance->early_leaving = $earlyLeaving;
                $employeeAttendance->overtime = $overtime;
                $employeeAttendance->total_rest = '00:00:00';
                $employeeAttendance->created_by = \Auth::user()->creatorId();

                // Detect late creation: attendance date is before today
                $isLate = ($request->date < date('Y-m-d'));
                $employeeAttendance->is_late_update = $isLate;
                $employeeAttendance->late_update_count = $isLate ? 1 : 0;
                $employeeAttendance->save();

                if ($isLate) {
                    $this->notifyAdminLateAttendance($employeeAttendance, 'created');
                }

                return redirect()->route('attendanceemployee.index')->with('success', __('Employee attendance successfully created.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show()
    {
        return redirect()->route('attendanceemployee.index');
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit attendance')) {
            $attendanceEmployee = AttendanceEmployee::where('id', $id)->first();
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('attendance.edit', compact('attendanceEmployee', 'employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->type == 'Employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if (\Auth::user()->can('edit attendance')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required',
                    'date'        => 'required',
                    'clock_in'    => 'required',
                    'clock_out'   => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $attendanceEmployee = AttendanceEmployee::find($id);
            if (!$attendanceEmployee) {
                return redirect()->back()->with('error', __('Attendance record not found.'));
            }

            $startTime = Utility::getValByName('company_start_time');
            $endTime = Utility::getValByName('company_end_time');

            $date     = $request->date;
            $clockIn  = date('H:i:s', strtotime($request->clock_in));
            $clockOut = date('H:i:s', strtotime($request->clock_out));

            $totalLateSeconds = strtotime($date . ' ' . $clockIn) - strtotime($date . ' ' . $startTime);
            $totalLateSeconds = max($totalLateSeconds, 0);
            $hours = floor($totalLateSeconds / 3600);
            $mins  = floor($totalLateSeconds / 60 % 60);
            $secs  = floor($totalLateSeconds % 60);
            $late  = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            $totalEarlyLeavingSeconds = strtotime($date . ' ' . $endTime) - strtotime($date . ' ' . $clockOut);
            $hours        = floor($totalEarlyLeavingSeconds / 3600);
            $mins         = floor($totalEarlyLeavingSeconds / 60 % 60);
            $secs         = floor($totalEarlyLeavingSeconds % 60);
            $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            if (strtotime($date . ' ' . $clockOut) > strtotime($date . ' ' . $endTime)) {
                $totalOvertimeSeconds = strtotime($date . ' ' . $clockOut) - strtotime($date . ' ' . $endTime);
                $hours    = floor($totalOvertimeSeconds / 3600);
                $mins     = floor($totalOvertimeSeconds / 60 % 60);
                $secs     = floor($totalOvertimeSeconds % 60);
                $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            } else {
                $overtime = '00:00:00';
            }

            $attendanceEmployee->employee_id   = $request->employee_id;
            $attendanceEmployee->date          = $date;
            $attendanceEmployee->clock_in      = $clockIn;
            $attendanceEmployee->clock_out     = $clockOut;
            $attendanceEmployee->status        = 'Present';
            $attendanceEmployee->late          = $late;
            $attendanceEmployee->early_leaving = ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00';
            $attendanceEmployee->overtime      = $overtime;
            $attendanceEmployee->total_rest    = '00:00:00';

            // Detect late update: attendance date is before today
            $isLate = ($date < date('Y-m-d'));
            if ($isLate) {
                $attendanceEmployee->is_late_update = true;
                $attendanceEmployee->late_update_count = ($attendanceEmployee->late_update_count ?? 0) + 1;
            }

            $attendanceEmployee->save();

            if ($isLate) {
                $this->notifyAdminLateAttendance($attendanceEmployee, 'updated');
            }

            return redirect()->route('attendanceemployee.index')->with('success', __('Employee attendance successfully updated.'));
        }

        $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
        $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('date', date('Y-m-d'))->first();
        //        if(!empty($todayAttendance) && $todayAttendance->clock_out == '00:00:00')
        //        if($todayAttendance->clock_out == '00:00:00')
        //        {

        $startTime = Utility::getValByName('company_start_time');
        $endTime = Utility::getValByName('company_end_time');

        if (Auth::user()->type == 'Employee') {

            $date = date("Y-m-d");
            $time = date("H:i:s");
            //early Leaving

            $companyEndTimestamp = strtotime($date . ' ' . $endTime);
            $clockOutTimestamp = strtotime($date . ' ' . $time);

            if($clockOutTimestamp < $companyEndTimestamp){
                $totalEarlyLeavingSeconds = $companyEndTimestamp - $clockOutTimestamp;
                $hours = floor($totalEarlyLeavingSeconds / 3600);
                $mins = floor($totalEarlyLeavingSeconds / 60 % 60);
                $secs = floor($totalEarlyLeavingSeconds % 60);
                $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            } else {
                $earlyLeaving = '00:00:00';
            }

            if (time() > strtotime($date . $endTime)) {
                //Overtime
                $totalOvertimeSeconds = time() - strtotime($date . $endTime);
                $hours = floor($totalOvertimeSeconds / 3600);
                $mins = floor($totalOvertimeSeconds / 60 % 60);
                $secs = floor($totalOvertimeSeconds % 60);
                $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            } else {
                $overtime = '00:00:00';
            }

            $attendanceEmployee['clock_out'] = $time;
            $attendanceEmployee['early_leaving'] = $earlyLeaving;
            $attendanceEmployee['overtime'] = $overtime;

            if (!empty($request->date)) {
                $attendanceEmployee['date'] = $request->date;
            }
            AttendanceEmployee::where('id', $id)->update($attendanceEmployee);

            return redirect()->route('hrm.dashboard')->with('success', __('Employee successfully clock Out.'));
        } else {
            $date = date("Y-m-d");
            $clockout_time = date("H:i:s");
            //late
            $totalLateSeconds = strtotime($clockout_time) - strtotime($date . $startTime);

            $hours = abs(floor($totalLateSeconds / 3600));
            $mins = abs(floor($totalLateSeconds / 60 % 60));
            $secs = abs(floor($totalLateSeconds % 60));

            $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            //early Leaving
            $totalEarlyLeavingSeconds = strtotime($date . $endTime) - strtotime($clockout_time);
            $hours = floor($totalEarlyLeavingSeconds / 3600);
            $mins = floor($totalEarlyLeavingSeconds / 60 % 60);
            $secs = floor($totalEarlyLeavingSeconds % 60);
            $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            if (strtotime($clockout_time) > strtotime($date . $endTime)) {
                //Overtime
                $totalOvertimeSeconds = strtotime($clockout_time) - strtotime($date . $endTime);
                $hours = floor($totalOvertimeSeconds / 3600);
                $mins = floor($totalOvertimeSeconds / 60 % 60);
                $secs = floor($totalOvertimeSeconds % 60);
                $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            } else {
                $overtime = '00:00:00';
            }

            $attendanceEmployee = AttendanceEmployee::find($id);
            // $attendanceEmployee->employee_id   = $employeeId;
            // $attendanceEmployee->date          = $request->date;
            // $attendanceEmployee->clock_in      = $request->clock_in;
            $attendanceEmployee->clock_out = $clockout_time;
            $attendanceEmployee->late = $late;
            $attendanceEmployee->early_leaving = $earlyLeaving;
            $attendanceEmployee->overtime = $overtime;
            $attendanceEmployee->total_rest = '00:00:00';

            $attendanceEmployee->save();

            return redirect()->back()->with('success', __('Employee attendance successfully updated.'));
        }
        //        }
        //        else
        //        {
        //            return redirect()->back()->with('error', __('Employee are not allow multiple time clock in & clock for every day.'));
        //        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete attendance')) {
            $attendance = AttendanceEmployee::where('id', $id)->first();

            $attendance->delete();

            return redirect()->route('attendanceemployee.index')->with('success', __('Attendance successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function attendance(Request $request)
    {
        if (\Auth::user()->type == 'Employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $settings = Utility::settings();

        if ($settings['ip_restrict'] == 'on') {
            $userIp = request()->ip();
            $ip = IpRestrict::where('created_by', \Auth::user()->creatorId())->whereIn('ip', [$userIp])->first();
            if (empty($ip)) {
                return redirect()->back()->with('error', __('This ip is not allowed to clock in & clock out.'));
            }
        }
        $employeeId = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;

        $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('date', date('Y-m-d'))->orderBy('id', 'desc')->first();
        //        if(empty($todayAttendance))
        //        {

        $startTime = Utility::getValByName('company_start_time');
        $endTime = Utility::getValByName('company_end_time');

        // Find the last clocked out entry for the employee
        $lastClockOutEntry = AttendanceEmployee::orderBy('id', 'desc')
            ->where('employee_id', '=', $employeeId)
            ->where('clock_out', '!=', '00:00:00')
            ->where('date', '=', date('Y-m-d'))
            ->first();

        $date = date("Y-m-d");
        $time = date("H:i:s");

        if($lastClockOutEntry != null) {
            $lastClockOutTime = $lastClockOutEntry->clock_out;
            $actualClockInTime = $date . ' ' . $time;

            $totalLateSeconds = strtotime($actualClockInTime) - strtotime($date . ' ' . $lastClockOutTime);

            // Ensure late time is non-negative
            $totalLateSeconds = max($totalLateSeconds, 0);

            $hours = abs(floor($totalLateSeconds / 3600));
            $mins = abs(floor($totalLateSeconds / 60 % 60));
            $secs = abs(floor($totalLateSeconds % 60));
            $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        } else {
            $expectedStartTime = $date . ' ' . $startTime;
            $actualClockInTime = $date . ' ' . $time;

            $totalLateSeconds = strtotime($actualClockInTime) - strtotime($expectedStartTime);

            $totalLateSeconds = max($totalLateSeconds, 0);

            $hours = abs(floor($totalLateSeconds / 3600));
            $mins = abs(floor($totalLateSeconds / 60 % 60));
            $secs = abs(floor($totalLateSeconds % 60));
            $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        }

        $checkDb = AttendanceEmployee::where('employee_id', '=', \Auth::user()->id)->get()->toArray();

        if (empty($checkDb)) {
            $employeeAttendance = new AttendanceEmployee();
            $employeeAttendance->employee_id = $employeeId;
            $employeeAttendance->date = $date;
            $employeeAttendance->status = 'Present';
            $employeeAttendance->clock_in = $time;
            $employeeAttendance->clock_out = '00:00:00';
            $employeeAttendance->late = $late;
            $employeeAttendance->early_leaving = '00:00:00';
            $employeeAttendance->overtime = '00:00:00';
            $employeeAttendance->total_rest = '00:00:00';
            $employeeAttendance->created_by = \Auth::user()->id;

            $employeeAttendance->save();

            return redirect()->back()->with('success', __('Employee Successfully Clock In.'));
        }
        foreach ($checkDb as $check) {

            $employeeAttendance = new AttendanceEmployee();
            $employeeAttendance->employee_id = $employeeId;
            $employeeAttendance->date = $date;
            $employeeAttendance->status = 'Present';
            $employeeAttendance->clock_in = $time;
            $employeeAttendance->clock_out = '00:00:00';
            $employeeAttendance->late = $late;
            $employeeAttendance->early_leaving = '00:00:00';
            $employeeAttendance->overtime = '00:00:00';
            $employeeAttendance->total_rest = '00:00:00';
            $employeeAttendance->created_by = \Auth::user()->id;

            $employeeAttendance->save();

            return redirect()->back()->with('success', __('Employee Successfully Clock In.'));

        }
        //        }
        //        else
        //        {
        //            return redirect()->back()->with('error', __('Employee are not allow multiple time clock in & clock for every day.'));
        //        }
    }

    public function bulkAttendance(Request $request)
    {
        if (\Auth::user()->can('create attendance')) {

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('All Departments', '');

            $employeesQuery = Employee::where('created_by', \Auth::user()->creatorId());
            
            if (!empty($request->department)) {
                $employeesQuery->where('department_id', $request->department);
            }
            
            $employees = $employeesQuery->get();

            return view('attendance.bulk', compact('employees', 'department'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bulkAttendanceData(Request $request)
    {
        if (\Auth::user()->can('create attendance')) {
            $date = $request->date;
            $employees = $request->employee_id;

            if (!empty($employees)) {
                foreach ($employees as $employee) {
                    $statusKey = 'status-' . $employee;
                    $inKey     = 'in-' . $employee;
                    $outKey    = 'out-' . $employee;
                    $workingKey = 'working_hours-' . $employee;
                    
                    $status = $request->$statusKey ?? 'Absent';
                    
                    $attendance = AttendanceEmployee::where('employee_id', '=', $employee)->where('date', '=', $request->date)->first();
                    if (empty($attendance)) {
                        $attendance = new AttendanceEmployee();
                        $attendance->employee_id = $employee;
                        $attendance->created_by = \Auth::user()->creatorId();
                        $attendance->date = $request->date;
                    }

                    $emp = Employee::find($employee);
                    if($emp) {
                        $attendance->department_id = $emp->department_id;
                    }

                    $attendance->status = $status;
                    
                    if ($status == 'Present' || $status == 'Late') {
                        $in  = $request->$inKey;
                        $out = $request->$outKey;
                        
                        $attendance->clock_in      = $in;
                        $attendance->clock_out     = $out;
                        
                        // Calculate working hours
                        $startTime = strtotime($in);
                        $endTime   = strtotime($out);
                        
                        if($endTime > $startTime) {
                            $diff = $endTime - $startTime;
                            $hours = round($diff / 3600, 2);
                            $attendance->working_hours = $hours;
                        } else {
                            $attendance->working_hours = 0;
                        }
                    } else {
                        $attendance->clock_in      = '00:00:00';
                        $attendance->clock_out     = '00:00:00';
                        $attendance->working_hours = 0;
                    }

                    $attendance->save();
                }

                return redirect()->back()->with('success', __('Employee attendance successfully processed.'));
            } else {
                return redirect()->back()->with('error', __('Employee not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    //for attendance employee report

    /**
     * Send a notification to the company owner when attendance is created/updated for a past date.
     */
    private function notifyAdminLateAttendance(AttendanceEmployee $attendance, string $action): void
    {
        $creatorId = \Auth::user()->creatorId();
        $employee  = $attendance->employee;
        $employeeName = $employee ? $employee->name : 'Employee #' . $attendance->employee_id;

        $notification          = new Notification();
        $notification->user_id = $creatorId;
        $notification->type    = 'late_attendance_update';
        $notification->data    = json_encode([
            'updated_by'      => \Auth::user()->id,
            'employee_name'   => $employeeName,
            'attendance_date' => $attendance->date,
            'action'          => $action,
        ]);
        $notification->is_read = 0;
        $notification->save();
    }

    /**
     * Show a log of all late attendance creates/updates for the admin.
     */
    public function lateAttendanceLog(Request $request)
    {
        if (!\Auth::user()->can('manage attendance')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = \Auth::user()->creatorId();

        $query = AttendanceEmployee::with('employee')
            ->where('is_late_update', true)
            ->whereIn('employee_id', function ($q) use ($creatorId) {
                $q->select('id')->from('employees')->where('created_by', $creatorId);
            })
            ->orderBy('updated_at', 'desc');

        if (!empty($request->from_date)) {
            $query->where('date', '>=', $request->from_date);
        }
        if (!empty($request->to_date)) {
            $query->where('date', '<=', $request->to_date);
        }

        $lateRecords   = $query->paginate(30);
        $totalLateCount = AttendanceEmployee::where('is_late_update', true)
            ->whereIn('employee_id', function ($q) use ($creatorId) {
                $q->select('id')->from('employees')->where('created_by', $creatorId);
            })->count();

        return view('attendance.late_log', compact('lateRecords', 'totalLateCount'));
    }

    //for attendance employee report

    public function importFile()
    {
        return view('attendance.import');
    }

    public function attendanceImportdata(Request $request)
    {
        session_start();
        $html = '<h3 class="text-danger text-center">Below data is not inserted</h3></br>';
        $flag = 0;
        $html .= '<table class="table table-bordered"><tr>';
        try {
            $request = $request->data;
            $file_data = $_SESSION['file_data'];

            unset($_SESSION['file_data']);
        } catch (\Throwable $th) {
            $html = '<h3 class="text-danger text-center">Something went wrong, Please try again</h3></br>';
            return response()->json([
                'html' => true,
                'response' => $html,
            ]);
        }
        $user = Auth::user();

        $startTime = Utility::getValByName('company_start_time');
        $endTime = Utility::getValByName('company_end_time');

        foreach ($file_data as $key => $row) {
            $employeeData = Employee::Where('email', 'like', $row[$request['employee_email']])->where('created_by', \Auth::user()->creatorId())->first();

            if (!empty($employeeData)) {
                try {

                    $employeeId = $employeeData->id;

                    $clockIn = $row[$request['clock_in']];
                    $clockOut = $row[$request['clock_out']];

                    if ($clockIn) {
                        $status = "present";
                    } else {
                        $status = "leave";
                    }

                    $totalLateSeconds = strtotime($clockIn) - strtotime($startTime);

                    $hours = floor($totalLateSeconds / 3600);
                    $mins = floor($totalLateSeconds / 60 % 60);
                    $secs = floor($totalLateSeconds % 60);
                    $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                    $totalEarlyLeavingSeconds = strtotime($endTime) - strtotime($clockOut);
                    $hours = floor($totalEarlyLeavingSeconds / 3600);
                    $mins = floor($totalEarlyLeavingSeconds / 60 % 60);
                    $secs = floor($totalEarlyLeavingSeconds % 60);
                    $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

                    if (strtotime($clockOut) > strtotime($endTime)) {
                        //Overtime
                        $totalOvertimeSeconds = strtotime($clockOut) - strtotime($endTime);
                        $hours = floor($totalOvertimeSeconds / 3600);
                        $mins = floor($totalOvertimeSeconds / 60 % 60);
                        $secs = floor($totalOvertimeSeconds % 60);
                        $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                    } else {
                        $overtime = '00:00:00';
                    }

                    $check = AttendanceEmployee::where('employee_id', $employeeId)->where('date', $row[$request['date']])->first();
                    if ($check) {
                        $check->update([
                            'late' => $late,
                            'early_leaving' => ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00',
                            'overtime' => $overtime,
                            'clock_in' => $row[$request['clock_in']],
                            'clock_out' => $row[$request['clock_out']],
                        ]);
                    } else {
                        $time_sheet = AttendanceEmployee::create([
                            'employee_id' => $employeeId,
                            'date' => $row[$request['date']],
                            'status' => $status,
                            'late' => $late,
                            'early_leaving' => ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00',
                            'overtime' => $overtime,
                            'clock_in' => $row[$request['clock_in']],
                            'clock_out' => $row[$request['clock_out']],
                            'created_by' => \Auth::user()->id,
                        ]);
                    }

                } catch (\Exception $e) {
                    $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . (isset($row[$request['employee_email']]) ? $row[$request['employee_email']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['date']]) ? $row[$request['date']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['clock_in']]) ? $row[$request['clock_in']] : '-') . '</td>';
                    $html .= '<td>' . (isset($row[$request['clock_out']]) ? $row[$request['clock_out']] : '-') . '</td>';

                    $html .= '</tr>';
                }
            } else {
                $flag = 1;
                $html .= '<tr>';

                $html .= '<td>' . (isset($row[$request['employee_email']]) ? $row[$request['employee_email']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['date']]) ? $row[$request['date']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['clock_in']]) ? $row[$request['clock_in']] : '-') . '</td>';
                $html .= '<td>' . (isset($row[$request['clock_out']]) ? $row[$request['clock_out']] : '-') . '</td>';

                $html .= '</tr>';
            }
        }

        $html .= '
                        </table>
                        <br />
                        ';
        if ($flag == 1) {

            return response()->json([
                'html' => true,
                'response' => $html,
            ]);
        } else {
            return response()->json([
                'html' => false,
                'response' => 'Data Imported Successfully',
            ]);
        }

    }
}
