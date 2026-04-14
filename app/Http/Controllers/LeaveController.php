<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveAttachment;
use App\Models\LeaveType;
use App\Models\Utility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {

        if(\Auth::user()->can('manage leave'))
        {
            if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
            {
                $leaves = Leave::where('created_by', '=', \Auth::user()->creatorId())
                    ->with(['leaveType','employees'])
                    ->withCount('attachments')
                    ->get();

            }
            else
            {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->first();
                $leaves   = Leave::where('employee_id', '=', $employee->id)
                    ->with(['leaveType','employees'])
                    ->withCount('attachments')
                    ->get();
            }

            return view('leave.index', compact('leaves'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create leave'))
        {
            $employee_id = null;
            if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
            {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }
            else
            {
                $employees = Employee::where('user_id', '=', \Auth::user()->id)->get()->pluck('name', 'id');
                $employee = \Auth::user()->employee;
                $employee_id = isset($employee) ? $employee->id : null;
            }
            $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('leave.create', compact('employees', 'leavetypes', 'employee_id'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {

        if(\Auth::user()->can('create leave'))
        {
            $leave_type = LeaveType::find($request->leave_type_id);

            // Build validation rules
            $rules = [
                'leave_type_id' => 'required',
                'start_date'    => 'required|date',
                'end_date'      => 'required|date|after_or_equal:start_date',
                'leave_reason'  => 'required',
                'remark'        => 'required',
            ];

            if ($leave_type && $leave_type->is_attachment_required) {
                $rules['attachments'] = 'required';
                $rules['attachments.*'] = 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
            } else {
                $rules['attachments.*'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
            }

            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // Advance days rule
            if ($leave_type && $leave_type->min_advance_days > 0) {
                $minDate = now()->addDays($leave_type->min_advance_days)->startOfDay();
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                if ($startDate->lt($minDate)) {
                    return redirect()->back()->with('error', __(
                        'Leave start date must be at least :days days from today for :type.',
                        ['days' => $leave_type->min_advance_days, 'type' => $leave_type->title]
                    ));
                }
            }

            $employee = Employee::where('user_id', '=', Auth::user()->id)->first();

            $startDate = new \DateTime($request->start_date);
            $endDate   = new \DateTime($request->end_date);
            $endDate->add(new \DateInterval('P1D'));
            $total_leave_days = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;

            if ($leave_type && $leave_type->days >= $total_leave_days)
            {
                $leave = new Leave();
                if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
                {
                    $leave->employee_id = $request->employee_id;
                }
                else
                {
                    $leave->employee_id = $employee->id;
                }

                $leave->leave_type_id    = $request->leave_type_id;
                $leave->applied_on       = date('Y-m-d');
                $leave->start_date       = $request->start_date;
                $leave->end_date         = $request->end_date;
                $leave->total_leave_days = $total_leave_days;
                $leave->leave_reason     = $request->leave_reason;
                $leave->remark           = $request->remark;
                $leave->status           = 'Pending';
                $leave->created_by       = \Auth::user()->creatorId();

                $leave->save();

                // Handle file attachments
                if ($request->hasFile('attachments')) {
                    $files = $request->file('attachments');
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    $this->storeAttachments(array_filter($files), $leave);
                }

                if(\Auth::user()->type !='company' || \Auth::user()->type !='HR')
                {
                    $setting  = Utility::settings(\Auth::user()->creatorId());
                    $employeeModel = Employee::find($leave->employee_id);
                    $user = User::find($leave->created_by);
                    if(isset($setting['new_leave']) && $setting['new_leave'] ==1)
                    {
                            $leaveArr = [
                                'user_name'=>$user->name,
                                'start_date' => $leave->start_date,
                                'end_date' => $leave->end_date,
                                'leave_reason' => $leave->leave_reason,
                                'employee_name' => $employeeModel->name,
                            ];
                            $resp = Utility::sendEmailTemplate('new_leave', [$user->id => $user->email], $leaveArr);
                    }
                }
                return redirect()->route('leave.index')->with('success', __('Leave successfully created.'));
            } else {
                return redirect()->back()->with('error', __('Leave type ' . ($leave_type ? $leave_type->title : '') . ' allows a maximum of ' . ($leave_type ? $leave_type->days : 0) . ' days. Please make sure your selected days are within the limit.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Leave $leave)
    {
        if(\Auth::user()->can('manage leave'))
        {
            $leave->load(['leaveType', 'employees', 'attachments']);
            return view('leave.show', compact('leave'));
        }
        return redirect()->route('leave.index');
    }

    public function edit(Leave $leave)
    {
        if(\Auth::user()->can('edit leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                if(\Auth::user()->type =='company' || \Auth::user()->type =='HR')
                {
                    $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                }
                else
                {
                    $employees = Employee::where('user_id', '=', \Auth::user()->id)->get()->pluck('name', 'id');
                }

                $employee = $leave->employees;
                $employee_id = isset($employee) ? $employee->id : null;
                $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
                $leave->load('attachments');

                return view('leave.edit', compact('leave', 'employees', 'leavetypes', 'employee_id'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $leave)
    {

        $leave = Leave::find($leave);
        if(\Auth::user()->can('edit leave'))
        {
            if($leave->created_by == Auth::user()->creatorId())
            {
                $leave_type = LeaveType::find($request->leave_type_id);

                // Build validation rules
                $rules = [
                    'leave_type_id' => 'required',
                    'start_date'    => 'required|date',
                    'end_date'      => 'required|date|after_or_equal:start_date',
                    'leave_reason'  => 'required',
                    'remark'        => 'required',
                ];

                // For update: only require attachment if none already exist and type requires it
                $existingCount = $leave->attachments()->count();
                if ($leave_type && $leave_type->is_attachment_required && $existingCount === 0) {
                    $rules['attachments'] = 'required';
                }
                $rules['attachments.*'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';

                $validator = \Validator::make($request->all(), $rules);
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                // Advance days rule
                if ($leave_type && $leave_type->min_advance_days > 0) {
                    $minDate   = now()->addDays($leave_type->min_advance_days)->startOfDay();
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    if ($startDate->lt($minDate)) {
                        return redirect()->back()->with('error', __(
                            'Leave start date must be at least :days days from today for :type.',
                            ['days' => $leave_type->min_advance_days, 'type' => $leave_type->title]
                        ));
                    }
                }

                $startDate = new \DateTime($request->start_date);
                $endDate   = new \DateTime($request->end_date);
                $endDate->add(new \DateInterval('P1D'));
                $total_leave_days = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;

                if ($leave_type && $leave_type->days >= $total_leave_days)
                {

                    $leave->employee_id      = $request->employee_id;
                    $leave->leave_type_id    = $request->leave_type_id;
                    $leave->start_date       = $request->start_date;
                    $leave->end_date         = $request->end_date;
                    $leave->total_leave_days = $total_leave_days;
                    $leave->leave_reason     = $request->leave_reason;
                    $leave->remark           = $request->remark;

                    $leave->save();

                    // Handle new file attachments
                    if ($request->hasFile('attachments')) {
                        $files = $request->file('attachments');
                        if (!is_array($files)) {
                            $files = [$files];
                        }
                        $this->storeAttachments(array_filter($files), $leave);
                    }

                    return redirect()->route('leave.index')->with('success', __('Leave successfully updated.'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Leave type ' . ($leave_type ? $leave_type->title : '') . ' allows a maximum of ' . ($leave_type ? $leave_type->days : 0) . ' days. Please make sure your selected days are within the limit.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Leave $leave)
    {
        if(\Auth::user()->can('delete leave'))
        {
            if($leave->created_by == \Auth::user()->creatorId())
            {
                // Delete all associated attachment files
                foreach ($leave->attachments as $attachment) {
                    $this->deleteAttachmentFile($attachment);
                }

                $leave->delete();

                return redirect()->route('leave.index')->with('success', __('Leave successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function action($id)
    {
        $leave     = Leave::with(['attachments', 'leaveType'])->find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);

        return view('leave.action', compact('employee', 'leavetype', 'leave'));
    }

    public function changeaction(Request $request)
    {
        if (!\Auth::user()->can('manage leave')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $leave = Leave::find($request->leave_id);

        $leave->status = $request->status;
        if($leave->status == 'Approval')
        {
            $leavetype = LeaveType::find($leave->leave_type_id);

            // Enforce advance days rule on approval
            if ($leavetype && $leavetype->min_advance_days > 0) {
                $minDate   = now()->addDays($leavetype->min_advance_days)->startOfDay();
                $startDate = Carbon::parse($leave->start_date)->startOfDay();
                if ($startDate->lt($minDate)) {
                    return redirect()->back()->with('error', __(
                        'Annual/Advance leave cannot be approved: the start date must be at least :days days from today.',
                        ['days' => $leavetype->min_advance_days]
                    ));
                }
            }

            $startDate               = new \DateTime($leave->start_date);
            $endDate                 = new \DateTime($leave->end_date);
            $endDate->add(new \DateInterval('P1D'));
            $total_leave_days        = $startDate->diff($endDate)->days;
            $leave->total_leave_days = $total_leave_days;
            $leave->status           = 'Approved';
        }

        $leave->save();


       //Send Email
        $setings = Utility::settings();
        if(!empty($employee->id))
        {
            if($setings['leave_action_sent'] == 1)
            {

                $employee     = Employee::where('id', $leave->employee_id)->where('created_by', '=', \Auth::user()->creatorId())->first();
                $leave->name  = !empty($employee->name) ? $employee->name : '';
                $leave->email = !empty($employee->email) ? $employee->email : '';

                $actionArr = [

                    'leave_name'=> !empty($employee->name) ? $employee->name : '',
                    'leave_status' => $leave->status,
                    'leave_reason' =>  $leave->leave_reason,
                    'leave_start_date' => $leave->start_date,
                    'leave_end_date' => $leave->end_date,
                    'total_leave_days' => $leave->total_leave_days,
                ];
                $resp = Utility::sendEmailTemplate('leave_action_sent', [$employee->id => $employee->email], $actionArr);


                return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }

        }

        return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'));
    }


    public function jsoncount(Request $request)
    {

        $leave_counts=[];
        $leave_types = LeaveType::where('created_by',\Auth::user()->creatorId())->get();
        foreach ($leave_types as  $type) {

            $year = date('Y');
            $counts = Leave::select(\DB::raw('
                COALESCE(SUM(
                    CASE
                        WHEN YEAR(start_date) = ? AND YEAR(end_date) = ? THEN total_leave_days
                        WHEN YEAR(start_date) = ? THEN DATEDIFF(LAST_DAY(start_date), start_date) + 1
                        WHEN YEAR(end_date) = ? THEN DATEDIFF(end_date, DATE_FORMAT(end_date, "%Y-01-01")) + 1
                        ELSE 0
                    END
                ), 0) AS total_leave
            '))
            ->where('leave_type_id', $type->id)
            ->where('employee_id', $request->employee_id)
            ->where('status', '!=', 'Reject')
            ->where(function ($query) use ($year) {
                $query->whereYear('start_date', $year)
                    ->orWhereYear('end_date', $year);
            })
            ->addBinding([$year, $year, $year, $year], 'select')
            ->first();

            $leave_count['total_leave']=!empty($counts)?$counts['total_leave']:0;
            $leave_count['title']=$type->title;
            $leave_count['days']=$type->days;
            $leave_count['id']=$type->id;
            $leave_count['is_attachment_required'] = $type->is_attachment_required;
            $leave_count['min_advance_days'] = $type->min_advance_days;
            $leave_counts[]=$leave_count;
        }

        return $leave_counts;

    }

    /**
     * Download a leave attachment.
     */
    public function downloadAttachment($id)
    {
        $attachment = LeaveAttachment::findOrFail($id);
        $leave = Leave::find($attachment->leave_id);

        if (!$leave || $leave->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $settings = Utility::getStorageSetting();

        if (!empty($settings['storage_setting']) && $settings['storage_setting'] == 'wasabi') {
            if (\Storage::disk('wasabi')->exists($attachment->file_path)) {
                return response()->streamDownload(function () use ($attachment) {
                    echo \Storage::disk('wasabi')->get($attachment->file_path);
                }, $attachment->original_name);
            }
        } elseif (!empty($settings['storage_setting']) && $settings['storage_setting'] == 's3') {
            if (\Storage::disk('s3')->exists($attachment->file_path)) {
                return response()->streamDownload(function () use ($attachment) {
                    echo \Storage::disk('s3')->get($attachment->file_path);
                }, $attachment->original_name);
            }
        } else {
            $filePath = public_path($attachment->file_path);
            if (file_exists($filePath)) {
                return response()->download($filePath, $attachment->original_name);
            }
        }

        return redirect()->back()->with('error', __('File not found.'));
    }

    /**
     * Delete a single leave attachment.
     * Returns JSON when the request expects JSON (AJAX), otherwise redirects.
     */
    public function deleteAttachment($id)
    {
        $attachment = LeaveAttachment::findOrFail($id);
        $leave = Leave::find($attachment->leave_id);

        if (!$leave || $leave->created_by != \Auth::user()->creatorId()) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => __('Permission denied.')], 403);
            }
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $this->deleteAttachmentFile($attachment);
        $attachment->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => __('Attachment successfully removed.')]);
        }
        return redirect()->back()->with('success', __('Attachment successfully removed.'));
    }

    /**
     * Helper: Store multiple uploaded files for a leave.
     */
    private function storeAttachments(array $files, Leave $leave)
    {
        $dir        = 'uploads/leave_attachments/';
        $settings   = Utility::getStorageSetting();
        $employeeId = $leave->employee_id;

        // Configure cloud disks if needed
        if (!empty($settings['storage_setting'])) {
            if ($settings['storage_setting'] == 'wasabi') {
                config([
                    'filesystems.disks.wasabi.key'      => $settings['wasabi_key'],
                    'filesystems.disks.wasabi.secret'   => $settings['wasabi_secret'],
                    'filesystems.disks.wasabi.region'   => $settings['wasabi_region'],
                    'filesystems.disks.wasabi.bucket'   => $settings['wasabi_bucket'],
                    'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                ]);
            } elseif ($settings['storage_setting'] == 's3') {
                config([
                    'filesystems.disks.s3.key'                  => $settings['s3_key'],
                    'filesystems.disks.s3.secret'               => $settings['s3_secret'],
                    'filesystems.disks.s3.region'               => $settings['s3_region'],
                    'filesystems.disks.s3.bucket'               => $settings['s3_bucket'],
                    'filesystems.disks.s3.use_path_style_endpoint' => false,
                ]);
            }
        }

        foreach ($files as $i => $file) {
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $timestamp    = time();
            $fileName     = $employeeId . '_' . $leave->id . '_' . $timestamp . '_' . $i . '.' . $extension;

            $storedPath = $dir . $fileName;

            if (!empty($settings['storage_setting']) && $settings['storage_setting'] == 'wasabi') {
                $storedPath = \Storage::disk('wasabi')->putFileAs($dir, $file, $fileName);
            } elseif (!empty($settings['storage_setting']) && $settings['storage_setting'] == 's3') {
                $storedPath = \Storage::disk('s3')->putFileAs($dir, $file, $fileName);
            } else {
                // Local storage — move to public dir so it is web-accessible
                $publicDir = public_path($dir);
                if (!file_exists($publicDir)) {
                    mkdir($publicDir, 0775, true);
                }
                $file->move($publicDir, $fileName);
                $storedPath = $dir . $fileName;
            }

            LeaveAttachment::create([
                'leave_id'      => $leave->id,
                'employee_id'   => $employeeId,
                'file_name'     => $fileName,
                'original_name' => $originalName,
                'file_path'     => $storedPath,
                'created_by'    => \Auth::user()->creatorId(),
            ]);
        }
    }

    /**
     * Helper: Delete a physical file for an attachment record.
     */
    private function deleteAttachmentFile(LeaveAttachment $attachment)
    {
        $settings = Utility::getStorageSetting();

        if (!empty($settings['storage_setting']) && $settings['storage_setting'] == 'wasabi') {
            \Storage::disk('wasabi')->delete($attachment->file_path);
        } elseif (!empty($settings['storage_setting']) && $settings['storage_setting'] == 's3') {
            \Storage::disk('s3')->delete($attachment->file_path);
        } else {
            $filePath = public_path($attachment->file_path);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }
}
