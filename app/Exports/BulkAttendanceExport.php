<?php

namespace App\Exports;

use App\Models\AttendanceEmployee;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BulkAttendanceExport implements FromCollection, WithHeadings
{
    protected $start_date;
    protected $end_date;
    protected $department_id;

    public function __construct($start_date, $end_date, $department_id = null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->department_id = $department_id;
    }

    public function collection()
    {
        $query = AttendanceEmployee::where('created_by', \Auth::user()->creatorId())
            ->whereBetween('date', [$this->start_date, $this->end_date]);

        if (!empty($this->department_id)) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', $this->department_id);
            });
        }

        $attendances = $query->with('employee', 'employee.department')->orderBy('date', 'asc')->get();

        $data = new Collection();
        foreach ($attendances as $attendance) {
            $data->push([
                'employee_id'   => \Auth::user()->employeeIdFormat($attendance->employee->employee_id ?? ''),
                'employee_name' => $attendance->employee->name ?? '',
                'department'    => $attendance->employee->department->name ?? '',
                'date'          => \Carbon\Carbon::parse($attendance->date)->format('Y-m-d'),
                'status'        => $attendance->status,
                'clock_in'      => $attendance->clock_in != '00:00:00' ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-',
                'clock_out'     => $attendance->clock_out != '00:00:00' ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-',
                'working_hours' => $attendance->working_hours != '00:00:00' ? $attendance->working_hours : '-',
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "Employee ID",
            "Name",
            "Department",
            "Date",
            "Status",
            "Clock In",
            "Clock Out",
            "Working Hours",
        ];
    }
}
