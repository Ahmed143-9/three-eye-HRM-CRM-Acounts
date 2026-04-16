<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\EmployeeAsset;
use App\Models\AssetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Display assets dashboard with statistics
     */
    public function index()
    {
        if(\Auth::user()->can('manage assets'))
        {
            $creatorId = \Auth::user()->creatorId();
            
            // Get statistics
            $totalAssets = Asset::where('created_by', $creatorId)->count();
            $availableAssets = Asset::where('created_by', $creatorId)->where('status', 'Available')->count();
            $assignedAssets = Asset::where('created_by', $creatorId)->where('status', 'Assigned')->count();
            $maintenanceAssets = Asset::where('created_by', $creatorId)->where('status', 'Maintenance')->count();
            
            // Get all assets with relationships
            $assets = Asset::where('created_by', $creatorId)
                          ->with(['currentAssignment.employee'])
                          ->latest()
                          ->get();

            return view('assets.index', compact(
                'assets',
                'totalAssets',
                'availableAssets',
                'assignedAssets',
                'maintenanceAssets'
            ));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new asset
     */
    public function create()
    {
        if(\Auth::user()->can('create assets'))
        {
            return view('assets.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created asset
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('create assets'))
        {
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required|string|max:255',
                    'category' => 'required|in:IT,Furniture,Electronics,Vehicles,Machinery,Other',
                    'condition' => 'required|in:New,Used,Damaged,Under Maintenance',
                    'purchase_date' => 'required|date',
                    'amount' => 'required|numeric|min:0',
                    'status' => 'required|in:Available,Assigned,Lost,Maintenance',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]
            );
            
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $asset = new Asset();
            $asset->name = $request->name;
            $asset->category = $request->category;
            $asset->condition = $request->condition;
            $asset->status = $request->status;
            $asset->purchase_date = $request->purchase_date;
            $asset->supported_date = $request->supported_date;
            $asset->amount = $request->amount;
            $asset->description = $request->description;
            $asset->manufacturer = $request->manufacturer;
            $asset->model_number = $request->model_number;
            $asset->serial_number = $request->serial_number;
            $asset->location = $request->location;
            $asset->warranty_until = $request->warranty_until;
            $asset->created_by = \Auth::user()->creatorId();

            // Handle image upload
            if ($request->hasFile('image')) {
                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('uploads/assets'), $imageName);
                $asset->image = $imageName;
            }

            $asset->save();

            return redirect()->route('account-assets.index')->with('success', __('Asset successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified asset
     */
    public function show($id)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $asset = Asset::where('created_by', \Auth::user()->creatorId())
                         ->with(['employeeAssets.employee', 'employeeAssets.assignedBy'])
                         ->findOrFail($id);
            
            $assignmentHistory = $asset->employeeAssets()->latest()->get();
            
            return view('assets.show', compact('asset', 'assignmentHistory'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified asset
     */
    public function edit($id)
    {
        if(\Auth::user()->can('edit assets'))
        {
            $asset = Asset::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
            
            return view('assets.edit', compact('asset'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified asset
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit assets'))
        {
            $asset = Asset::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
            
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required|string|max:255',
                    'category' => 'required|in:IT,Furniture,Electronics,Vehicles,Machinery,Other',
                    'condition' => 'required|in:New,Used,Damaged,Under Maintenance',
                    'purchase_date' => 'required|date',
                    'amount' => 'required|numeric|min:0',
                    'status' => 'required|in:Available,Assigned,Lost,Maintenance',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]
            );
            
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $asset->name = $request->name;
            $asset->category = $request->category;
            $asset->condition = $request->condition;
            $asset->status = $request->status;
            $asset->purchase_date = $request->purchase_date;
            $asset->supported_date = $request->supported_date;
            $asset->amount = $request->amount;
            $asset->description = $request->description;
            $asset->manufacturer = $request->manufacturer;
            $asset->model_number = $request->model_number;
            $asset->serial_number = $request->serial_number;
            $asset->location = $request->location;
            $asset->warranty_until = $request->warranty_until;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($asset->image) {
                    $oldImagePath = public_path('uploads/assets/' . $asset->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('uploads/assets'), $imageName);
                $asset->image = $imageName;
            }

            $asset->save();

            return redirect()->route('account-assets.index')->with('success', __('Asset successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified asset
     */
    public function destroy($id)
    {
        if(\Auth::user()->can('delete assets'))
        {
            $asset = Asset::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
            
            // Check if asset is currently assigned
            $activeAssignment = EmployeeAsset::where('asset_id', $asset->id)
                                            ->whereNull('return_date')
                                            ->where('status', 'Assigned')
                                            ->first();
            
            if ($activeAssignment) {
                return redirect()->back()->with('error', __('Cannot delete asset that is currently assigned. Please return the asset first.'));
            }
            
            // Delete image if exists
            if ($asset->image) {
                $imagePath = public_path('uploads/assets/' . $asset->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $asset->delete();

            return redirect()->route('account-assets.index')->with('success', __('Asset successfully deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show assign asset form
     */
    public function showAssignForm($id)
    {
        if(\Auth::user()->can('assign assets'))
        {
            $asset = Asset::where('created_by', \Auth::user()->creatorId())
                         ->where('status', 'Available')
                         ->findOrFail($id);
            
            $employees = Employee::where('created_by', \Auth::user()->creatorId())
                                ->pluck('name', 'id');
            
            return view('assets.assign', compact('asset', 'employees'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Assign asset to employee
     */
    public function assignAsset(Request $request, $id)
    {
        if(\Auth::user()->can('assign assets'))
        {
            $asset = Asset::where('created_by', \Auth::user()->creatorId())
                         ->findOrFail($id);
            
            // Validate
            $validator = Validator::make(
                $request->all(), [
                    'employee_id' => 'required|exists:employees,id',
                    'assign_date' => 'required|date',
                    'remarks' => 'nullable|string',
                    'document' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:2048',
                ]
            );
            
            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->getMessageBag()->first())->withInput();
            }

            // Check if asset is available
            if (!$asset->isAvailable()) {
                return redirect()->back()->with('error', __('This asset is not available for assignment.'));
            }

            // Check for duplicate active assignment
            $existingAssignment = EmployeeAsset::where('asset_id', $asset->id)
                                              ->whereNull('return_date')
                                              ->where('status', 'Assigned')
                                              ->first();
            
            if ($existingAssignment) {
                return redirect()->back()->with('error', __('This asset is already assigned to an employee.'));
            }

            // Create assignment
            $assignment = new EmployeeAsset();
            $assignment->employee_id = $request->employee_id;
            $assignment->asset_id = $asset->id;
            $assignment->assign_date = $request->assign_date;
            $assignment->status = 'Assigned';
            $assignment->remarks = $request->remarks;
            $assignment->assigned_by = \Auth::user()->id;
            $assignment->created_by = \Auth::user()->creatorId();

            // Handle document upload
            if ($request->hasFile('document')) {
                $docName = time() . '_' . $request->file('document')->getClientOriginalName();
                $request->file('document')->move(public_path('uploads/asset_documents'), $docName);
                $assignment->document = $docName;
            }

            $assignment->save();

            // Update asset status
            $asset->status = 'Assigned';
            $asset->save();

            return redirect()->route('account-assets.index')->with('success', __('Asset successfully assigned to employee.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show return asset form
     */
    public function showReturnForm($id)
    {
        if(\Auth::user()->can('return assets'))
        {
            $assignment = EmployeeAsset::whereHas('asset', function($query) {
                $query->where('created_by', \Auth::user()->creatorId());
            })
            ->where('asset_id', $id)
            ->whereNull('return_date')
            ->where('status', 'Assigned')
            ->with(['asset', 'employee'])
            ->firstOrFail();
            
            return view('assets.return', compact('assignment'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Return asset from employee
     */
    public function returnAsset(Request $request, $id)
    {
        if(\Auth::user()->can('return assets'))
        {
            $assignment = EmployeeAsset::whereHas('asset', function($query) {
                $query->where('created_by', \Auth::user()->creatorId());
            })
            ->where('asset_id', $id)
            ->whereNull('return_date')
            ->where('status', 'Assigned')
            ->firstOrFail();
            
            // Validate
            $validator = Validator::make(
                $request->all(), [
                    'return_date' => 'required|date',
                    'status' => 'required|in:Returned,Damaged,Lost',
                    'remarks' => 'nullable|string',
                ]
            );
            
            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->getMessageBag()->first())->withInput();
            }

            // Update assignment
            $assignment->return_date = $request->return_date;
            $assignment->status = $request->status;
            $assignment->remarks = $request->remarks;
            $assignment->save();

            // Update asset status
            $asset = $assignment->asset;
            if ($request->status === 'Returned') {
                $asset->status = 'Available';
                $asset->condition = 'Used';
            } elseif ($request->status === 'Damaged') {
                $asset->status = 'Maintenance';
                $asset->condition = 'Damaged';
            } elseif ($request->status === 'Lost') {
                $asset->status = 'Lost';
            }
            $asset->save();

            return redirect()->route('account-assets.index')->with('success', __('Asset successfully returned.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show asset history
     */
    public function history($id)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $asset = Asset::where('created_by', \Auth::user()->creatorId())
                         ->with(['employeeAssets.employee', 'employeeAssets.assignedBy'])
                         ->findOrFail($id);
            
            $history = $asset->employeeAssets()->latest()->get();
            
            return view('assets.history', compact('asset', 'history'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show employee assets
     */
    public function employeeAssets($employeeId)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $employee = Employee::where('created_by', \Auth::user()->creatorId())
                               ->findOrFail($employeeId);
            
            $currentAssets = EmployeeAsset::where('employee_id', $employeeId)
                                         ->whereNull('return_date')
                                         ->where('status', 'Assigned')
                                         ->with('asset')
                                         ->get();
            
            $pastAssets = EmployeeAsset::where('employee_id', $employeeId)
                                      ->whereNotNull('return_date')
                                      ->with('asset')
                                      ->latest()
                                      ->get();
            
            return view('assets.employee-assets', compact('employee', 'currentAssets', 'pastAssets'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Asset Requests Management
     */
    public function requests()
    {
        if(\Auth::user()->can('manage assets'))
        {
            $requests = AssetRequest::whereHas('asset', function($query) {
                $query->where('created_by', \Auth::user()->creatorId());
            })
            ->with(['employee', 'asset'])
            ->latest()
            ->get();
            
            return view('assets.requests', compact('requests'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Approve asset request
     */
    public function approveRequest($id)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $request = AssetRequest::whereHas('asset', function($query) {
                $query->where('created_by', \Auth::user()->creatorId());
            })
            ->where('status', 'Pending')
            ->findOrFail($id);
            
            // Check if asset is still available
            if (!$request->asset->isAvailable()) {
                return redirect()->back()->with('error', __('Asset is no longer available.'));
            }
            
            // Approve request
            $request->status = 'Approved';
            $request->approved_by = \Auth::user()->id;
            $request->approved_date = now();
            $request->save();
            
            return redirect()->back()->with('success', __('Asset request approved successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Reject asset request
     */
    public function rejectRequest(Request $request, $id)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $assetRequest = AssetRequest::whereHas('asset', function($query) {
                $query->where('created_by', \Auth::user()->creatorId());
            })
            ->where('status', 'Pending')
            ->findOrFail($id);
            
            $validator = Validator::make(
                $request->all(), [
                    'rejection_reason' => 'required|string',
                ]
            );
            
            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }
            
            // Reject request
            $assetRequest->status = 'Rejected';
            $assetRequest->rejection_reason = $request->rejection_reason;
            $assetRequest->approved_by = \Auth::user()->id;
            $assetRequest->approved_date = now();
            $assetRequest->save();
            
            return redirect()->back()->with('success', __('Asset request rejected.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
