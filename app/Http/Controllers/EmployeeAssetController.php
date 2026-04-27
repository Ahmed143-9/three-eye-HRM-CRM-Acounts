<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\EmployeeAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeAssetController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage assets')) {
            $assets = EmployeeAsset::where('created_by', \Auth::user()->creatorId())->with(['employee', 'asset'])->get();
            return view('employee_assets.index', compact('assets'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create assets')) {
            $employees = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $company_assets = Asset::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $company_assets->prepend(__('Select Asset (Optional)'), '');
            return view('employee_assets.create', compact('employees', 'company_assets'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create assets')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required',
                    'status' => 'required',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $asset = new EmployeeAsset();
            $asset->employee_id = $request->employee_id;
            $asset->asset_id = $request->asset_id;
            $asset->asset_name = $request->asset_name;
            $asset->description = $request->description;
            $asset->status = $request->status;
            $asset->created_by = \Auth::user()->creatorId();

            if ($request->hasFile('image')) {
                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('uploads/employee_assets'), $imageName);
                $asset->image = $imageName;
            }

            $asset->save();

            return redirect()->route('employee-assets.index')->with('success', __('Employee Asset successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(EmployeeAsset $employee_asset)
    {
        if (\Auth::user()->can('edit assets')) {
            $employees = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $company_assets = Asset::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $company_assets->prepend(__('Select Asset (Optional)'), '');
            return view('employee_assets.edit', compact('employee_asset', 'employees', 'company_assets'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, EmployeeAsset $employee_asset)
    {
        if (\Auth::user()->can('edit assets')) {
            $validator = Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required',
                    'status' => 'required',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $employee_asset->employee_id = $request->employee_id;
            $employee_asset->asset_id = $request->asset_id;
            $employee_asset->asset_name = $request->asset_name;
            $employee_asset->description = $request->description;
            $employee_asset->status = $request->status;

            if ($request->hasFile('image')) {
                // Delete old image
                if ($employee_asset->image && file_exists(public_path('uploads/employee_assets/' . $employee_asset->image))) {
                    unlink(public_path('uploads/employee_assets/' . $employee_asset->image));
                }

                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('uploads/employee_assets'), $imageName);
                $employee_asset->image = $imageName;
            }

            $employee_asset->save();

            return redirect()->route('employee-assets.index')->with('success', __('Employee Asset successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(EmployeeAsset $employee_asset)
    {
        if (\Auth::user()->can('delete assets')) {
            if ($employee_asset->image && file_exists(public_path('uploads/employee_assets/' . $employee_asset->image))) {
                unlink(public_path('uploads/employee_assets/' . $employee_asset->image));
            }
            $employee_asset->delete();
            return redirect()->route('employee-assets.index')->with('success', __('Employee Asset successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
