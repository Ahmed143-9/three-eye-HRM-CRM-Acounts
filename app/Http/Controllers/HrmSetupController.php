<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HrmSetupController extends Controller
{
    /**
     * AJAX: Quick-create a Department or Designation from Select2 tags mode.
     * POST /hrm-setup/quick-create
     */
    public function quickCreate(Request $request)
    {
        $type = $request->input('type'); // 'department' or 'designation'
        $name = trim($request->input('name', ''));

        if (empty($name)) {
            return response()->json(['success' => false, 'message' => __('Name is required.')], 422);
        }

        try {
            if ($type === 'department') {
                // Check for duplicate
                $existing = Department::where('created_by', Auth::user()->creatorId())
                    ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                    ->first();

                if ($existing) {
                    return response()->json(['success' => true, 'id' => $existing->id, 'name' => $existing->name]);
                }

                $dept = Department::create([
                    'name'       => $name,
                    'created_by' => Auth::user()->creatorId(),
                ]);
                return response()->json(['success' => true, 'id' => $dept->id, 'name' => $dept->name]);

            } elseif ($type === 'designation') {
                $departmentId = $request->input('department_id');

                $existing = Designation::where('created_by', Auth::user()->creatorId())
                    ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                    ->when($departmentId, fn($q) => $q->where('department_id', $departmentId))
                    ->first();

                if ($existing) {
                    return response()->json(['success' => true, 'id' => $existing->id, 'name' => $existing->name]);
                }

                $desig = Designation::create([
                    'name'          => $name,
                    'department_id' => $departmentId,
                    'created_by'    => Auth::user()->creatorId(),
                ]);
                return response()->json(['success' => true, 'id' => $desig->id, 'name' => $desig->name]);
            }

            return response()->json(['success' => false, 'message' => __('Invalid type.')], 422);

        } catch (\Throwable $e) {
            \Log::error('HrmSetup quickCreate error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('Error creating record.')], 500);
        }
    }

    /**
     * GET /hrm-setup/designations-by-department?department_id=X
     * Returns designations for a given department (for dynamic filtering).
     */
    public function designationsByDepartment(Request $request)
    {
        $deptId = $request->input('department_id');
        $designations = Designation::where('created_by', Auth::user()->creatorId())
            ->when($deptId, fn($q) => $q->where('department_id', $deptId))
            ->get(['id', 'name']);

        return response()->json($designations);
    }

    // ── Departments CRUD ─────────────────────────────────────────────────────

    public function departmentsIndex()
    {
        if (!Auth::user()->can('manage employee') && Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        $departments = Department::where('created_by', Auth::user()->creatorId())->latest()->get();
        return view('hrm_setup.departments', compact('departments'));
    }

    public function departmentsStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $dept = Department::create([
            'name'       => $request->name,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->back()->with('success', __('Department created successfully.'));
    }

    public function departmentsUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $dept = Department::findOrFail($id);
        $dept->update(['name' => $request->name]);
        return redirect()->back()->with('success', __('Department updated.'));
    }

    public function departmentsDestroy($id)
    {
        Department::findOrFail($id)->delete();
        return redirect()->back()->with('success', __('Department deleted.'));
    }

    // ── Designations CRUD ────────────────────────────────────────────────────

    public function designationsIndex()
    {
        if (!Auth::user()->can('manage employee') && Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        $designations = Designation::where('created_by', Auth::user()->creatorId())->with('department')->latest()->get();
        $departments  = Department::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        return view('hrm_setup.designations', compact('designations', 'departments'));
    }

    public function designationsStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Designation::create([
            'name'          => $request->name,
            'department_id' => $request->department_id,
            'created_by'    => Auth::user()->creatorId(),
        ]);

        return redirect()->back()->with('success', __('Designation created successfully.'));
    }

    public function designationsUpdate(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $desig = Designation::findOrFail($id);
        $desig->update(['name' => $request->name, 'department_id' => $request->department_id]);
        return redirect()->back()->with('success', __('Designation updated.'));
    }

    public function designationsDestroy($id)
    {
        Designation::findOrFail($id)->delete();
        return redirect()->back()->with('success', __('Designation deleted.'));
    }
}
