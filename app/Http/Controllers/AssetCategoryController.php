<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetCategoryController extends Controller
{
    /**
     * Display asset categories
     */
    public function index()
    {
        if(\Auth::user()->can('manage assets'))
        {
            $categories = AssetCategory::where('created_by', \Auth::user()->creatorId())
                                      ->withCount(['assets', 'assets as available_count' => function($query) {
                                          $query->where('status', 'Available');
                                      }, 'assets as assigned_count' => function($query) {
                                          $query->where('status', 'Assigned');
                                      }])
                                      ->latest()
                                      ->get();

            return view('assets.categories.index', compact('categories'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        if(\Auth::user()->can('manage assets'))
        {
            return view('assets.categories.create');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required|string|max:255|unique:asset_categories,name',
                    'code' => 'required|string|max:10|unique:asset_categories,code',
                    'color' => 'required|in:primary,secondary,success,danger,warning,info,dark',
                ]
            );
            
            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->getMessageBag()->first())->withInput();
            }

            $category = new AssetCategory();
            $category->name = $request->name;
            $category->code = strtoupper($request->code);
            $category->description = $request->description;
            $category->icon = $request->icon;
            $category->color = $request->color;
            $category->is_active = $request->has('is_active') ? true : false;
            $category->created_by = \Auth::user()->creatorId();
            $category->save();

            return redirect()->route('asset-categories.index')->with('success', __('Category created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit($id)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $category = AssetCategory::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
            return view('assets.categories.edit', compact('category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $category = AssetCategory::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
            
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required|string|max:255|unique:asset_categories,name,' . $id,
                    'code' => 'required|string|max:10|unique:asset_categories,code,' . $id,
                    'color' => 'required|in:primary,secondary,success,danger,warning,info,dark',
                ]
            );
            
            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->getMessageBag()->first())->withInput();
            }

            $category->name = $request->name;
            $category->code = strtoupper($request->code);
            $category->description = $request->description;
            $category->icon = $request->icon;
            $category->color = $request->color;
            $category->is_active = $request->has('is_active') ? true : false;
            $category->save();

            return redirect()->route('asset-categories.index')->with('success', __('Category updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        if(\Auth::user()->can('manage assets'))
        {
            $category = AssetCategory::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
            
            // Check if category has assets
            if ($category->assets()->count() > 0) {
                return redirect()->back()->with('error', __('Cannot delete category that has assets. Please reassign or delete the assets first.'));
            }
            
            $category->delete();

            return redirect()->route('asset-categories.index')->with('success', __('Category deleted successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Setup default categories for company
     */
    public function setupDefaults()
    {
        if(\Auth::user()->can('manage assets'))
        {
            $creatorId = \Auth::user()->creatorId();
            
            // Check if already setup
            $existingCount = AssetCategory::where('created_by', $creatorId)->count();
            
            if ($existingCount > 0) {
                return redirect()->back()->with('error', __('Default categories already exist for your company.'));
            }

            $defaultCategories = [
                [
                    'name' => 'IT Equipment',
                    'code' => 'IT',
                    'description' => 'Computers, laptops, monitors, keyboards, mice, and other IT equipment',
                    'icon' => 'ti ti-device-laptop',
                    'color' => 'primary',
                    'is_active' => true,
                    'created_by' => $creatorId,
                ],
                [
                    'name' => 'Furniture',
                    'code' => 'FURN',
                    'description' => 'Desks, chairs, cabinets, tables, and office furniture',
                    'icon' => 'ti ti-chair',
                    'color' => 'success',
                    'is_active' => true,
                    'created_by' => $creatorId,
                ],
                [
                    'name' => 'Electronics',
                    'code' => 'ELEC',
                    'description' => 'Phones, printers, cameras, projectors, and electronic devices',
                    'icon' => 'ti ti-device-mobile',
                    'color' => 'info',
                    'is_active' => true,
                    'created_by' => $creatorId,
                ],
                [
                    'name' => 'Vehicles',
                    'code' => 'VEH',
                    'description' => 'Company cars, trucks, bikes, and other vehicles',
                    'icon' => 'ti ti-car',
                    'color' => 'warning',
                    'is_active' => true,
                    'created_by' => $creatorId,
                ],
                [
                    'name' => 'Machinery',
                    'code' => 'MACH',
                    'description' => 'Industrial machines, tools, and equipment',
                    'icon' => 'ti ti-settings',
                    'color' => 'danger',
                    'is_active' => true,
                    'created_by' => $creatorId,
                ],
                [
                    'name' => 'Other',
                    'code' => 'OTHER',
                    'description' => 'Miscellaneous assets and equipment',
                    'icon' => 'ti ti-package',
                    'color' => 'secondary',
                    'is_active' => true,
                    'created_by' => $creatorId,
                ],
            ];

            foreach ($defaultCategories as $catData) {
                AssetCategory::create($catData);
            }

            return redirect()->route('asset-categories.index')->with('success', __('Default categories setup successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
