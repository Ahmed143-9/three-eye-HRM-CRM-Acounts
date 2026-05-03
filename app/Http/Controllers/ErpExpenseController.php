<?php

namespace App\Http\Controllers;

use App\Models\ErpExpenseUnit;
use Illuminate\Http\Request;
use App\Models\ErpExpense;
use App\Models\ErpExpenseCategory;
use App\Models\ErpExpenseItem;
use App\Models\ProductServiceUnit;
use App\Models\ErpExpenseAttachment;
use App\Models\ErpExpenseStatusLog;
use App\Models\Employee;
use App\Models\Transport;
use App\Models\Bill;
use App\Models\BillProduct;
use App\Models\Utility;
use App\Models\ErpSalarySheet;
use App\Models\Notification;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ErpExpenseController extends Controller
{
    public function index($type = 'purchase')
    {
        if (Auth::user()->can('manage expense') || Auth::user()->type == 'company') {
            $workspace_id = Auth::user()->currentWorkspace ?? 1;
            $expenses = ErpExpense::with(['category', 'employee'])
                ->where('workspace_id', $workspace_id)
                ->where('type', $type)
                ->orderBy('id', 'desc')
                ->get();
            return view('erp_expenses.index', compact('expenses', 'type'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function create(Request $request, $type)
    {
        if (Auth::user()->can('create expense') || Auth::user()->type == 'company') {
            $workspace_id = Auth::user()->currentWorkspace ?? 1;
            $categories = ErpExpenseCategory::where('module_type', $type)
                ->where('is_active', true)
                ->where(function($q) use ($workspace_id) {
                    $q->whereNull('workspace_id')
                      ->orWhere('workspace_id', $workspace_id);
                })
                ->pluck('name', 'id');
            $employees = Employee::get()->pluck('name', 'id');
            $units = ErpExpenseUnit::where('status', 1)->pluck('name', 'name');
            $transports = Transport::get()->pluck('id', 'id');

            $sheet = null;
            if ($request->has('sheet_id')) {
                $sheet = ErpSalarySheet::find($request->sheet_id);
            }
            
            return view('erp_expenses.create', compact('type', 'categories', 'employees', 'units', 'transports', 'sheet'));
        }
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function store(Request $request, $type)
    {
        if (Auth::user()->can('create expense') || Auth::user()->type == 'company') {
            $rules = [
                'erp_expense_category_id' => 'required',
                'date' => 'required|date',
                'employee_id' => 'required',
            ];

            if ($type == 'salary') {
                $rules['amount'] = 'required|numeric|min:0';
            }

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            DB::beginTransaction();
            try {
                $expense = new ErpExpense();
                $expense->serial_no = strtoupper(substr($type, 0, 3)) . '-' . time();
                $expense->type = $type;
                $expense->erp_expense_category_id = $request->erp_expense_category_id;
                $expense->date = $request->date;
                $expense->billing_month = $request->billing_month;
                $expense->description = $request->description;
                $expense->employee_id = $request->employee_id;
                
                if ($request->employee_id) {
                    $employee = Employee::find($request->employee_id);
                    if ($employee) {
                        $expense->department_id = $employee->department_id;
                        $expense->designation_id = $employee->designation_id;
                    }
                }
                
                $expense->supplier_id = $request->supplier_id;
                $expense->transport_id = $request->transport_id;
                $expense->trip_no = $request->trip_no;
                $expense->net_salary = $request->net_salary;
                $expense->deduction_amount = $request->deduction_amount;
                $expense->cause_of_deduction = $request->cause_of_deduction;
                $expense->erp_salary_sheet_id = $request->erp_salary_sheet_id;
                $expense->remarks = $request->remarks;
                $expense->workspace_id = Auth::user()->currentWorkspace ?? 1;
                $expense->created_by = Auth::user()->id;
                $expense->status = 'Pending Approval';

                if ($request->hasFile('attachment')) {
                    $fileNameToStore = time() . '_' . $request->file('attachment')->getClientOriginalName();
                    $dir = 'uploads/erp_expenses/';
                    $path = Utility::upload_file($request, 'attachment', $fileNameToStore, $dir, []);
                    if ($path['flag'] == 1) {
                        $expense->attachment = $path['url'];
                    }
                }

                // Initial amount set to 0 or request amount for non-itemized
                $expense->amount = $request->amount ?? 0;
                $expense->save();

                // Handle itemization
                $total_amount = 0;
                if ($request->has('items')) {
                    foreach ($request->items as $item) {
                        if (!empty($item['product_name'])) {
                            $unitId = null;
                            if (!empty($item['unit'])) {
                                $unitObj = \App\Models\ErpExpenseUnit::getUnit($item['unit']);
                                $unitId = $unitObj->id;
                            }
                            ErpExpenseItem::create([
                                'erp_expense_id' => $expense->id,
                                'product_name' => $item['product_name'],
                                'quantity' => $item['quantity'] ?? 1,
                                'unit_id' => $unitId,
                                'unit_price' => $item['unit_price'] ?? 0,
                                'amount' => $item['amount'] ?? 0,
                            ]);
                            $total_amount += ($item['amount'] ?? 0);
                        }
                    }
                }

                // Update total amount if items were added
                if ($total_amount > 0) {
                    $expense->amount = $total_amount;
                    $expense->save();
                }

                // Status Log
                ErpExpenseStatusLog::create([
                    'erp_expense_id' => $expense->id,
                    'status' => 'Pending Approval',
                    'comments' => 'Expense created and sent for Admin approval',
                    'user_id' => Auth::user()->id,
                ]);

                // Notify Admin (Find Admin/Company users)
                $admins = User::where('type', 'company')->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'expense_submitted',
                        'title' => ucfirst($type) . ' Bill: Pending Approval',
                        'message' => 'New ' . $type . ' expense of ' . Auth::user()->priceFormat($expense->amount) . ' submitted by ' . Auth::user()->name . '.',
                        'related_model' => 'ErpExpense',
                        'related_id' => $expense->id,
                        'created_by' => Auth::user()->id,
                        'is_read' => 0,
                    ]);
                }

                DB::commit();
                return redirect()->route('erp-expenses.index', $type)->with('success', __('Expense successfully created and sent for approval.'));
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Expense Store Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to store expense: ' . $e->getMessage());
            }
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function show($type, $id)
    {
        if (Auth::user()->can('manage expense') || Auth::user()->type == 'company') {
            $expense = ErpExpense::with(['category', 'employee', 'items.unit', 'statusLogs.user'])->find($id);
            if ($expense) {
                // Mark related notifications as read
                Notification::where('user_id', Auth::user()->id)
                    ->where('related_model', 'ErpExpense')
                    ->where('related_id', $expense->id)
                    ->where('is_read', 0)
                    ->update(['is_read' => 1]);

                return view('erp_expenses.show', compact('expense', 'type'));
            }
            return redirect()->back()->with('error', __('Expense not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function edit($type, $id)
    {
        if (Auth::user()->can('edit expense') || Auth::user()->type == 'company') {
            $expense = ErpExpense::with('items')->find($id);
            if ($expense) {
                $workspace_id = Auth::user()->currentWorkspace ?? 1;
                $categories = ErpExpenseCategory::where('module_type', $type)
                    ->where('is_active', true)
                    ->where(function($q) use ($workspace_id) {
                        $q->whereNull('workspace_id')
                          ->orWhere('workspace_id', $workspace_id);
                    })
                    ->pluck('name', 'id');
                $employees = Employee::get()->pluck('name', 'id');
                $units = ErpExpenseUnit::where('status', 1)->pluck('name', 'name');
                $transports = Transport::get()->pluck('id', 'id');
                
                return view('erp_expenses.edit', compact('expense', 'type', 'categories', 'employees', 'units', 'transports'));
            }
            return redirect()->back()->with('error', __('Expense not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function update(Request $request, $type, $id)
    {
        if (Auth::user()->can('edit expense') || Auth::user()->type == 'company') {
            $expense = ErpExpense::find($id);
            if ($expense) {
                if ($expense->status == 'Approved' || $expense->status == 'Paid') {
                    return redirect()->back()->with('error', __('Cannot edit an approved or paid expense.'));
                }

                $expense->erp_expense_category_id = $request->erp_expense_category_id;
                $expense->date = $request->date;
                $expense->billing_month = $request->billing_month;
                $expense->description = $request->description;
                $expense->employee_id = $request->employee_id;
                
                if ($request->employee_id) {
                    $employee = Employee::find($request->employee_id);
                    if ($employee) {
                        $expense->department_id = $employee->department_id;
                        $expense->designation_id = $employee->designation_id;
                    }
                }
                
                $expense->transport_id = $request->transport_id;
                $expense->remarks = $request->remarks;

                if (!empty($request->attachment)) {
                    $fileName = time() . "_" . $request->attachment->getClientOriginalName();
                    $dir = 'uploads/erp_expenses/';
                    $path = Utility::upload_file($request, 'attachment', $fileName, $dir, []);
                    if ($path['flag'] == 1) {
                        $expense->attachment = $path['url'];
                    }
                }

                $expense->amount = $request->amount ?? 0;
                $expense->save();

                // Clear old items and add new ones
                ErpExpenseItem::where('erp_expense_id', $expense->id)->delete();
                $total_amount = 0;
                if ($request->has('items')) {
                    foreach ($request->items as $item) {
                        if (!empty($item['product_name'])) {
                            $unitId = null;
                            if (!empty($item['unit'])) {
                                $unitObj = \App\Models\ErpExpenseUnit::getUnit($item['unit']);
                                $unitId = $unitObj->id;
                            }
                            ErpExpenseItem::create([
                                'erp_expense_id' => $expense->id,
                                'product_name' => $item['product_name'],
                                'quantity' => $item['quantity'] ?? 1,
                                'unit_id' => $unitId,
                                'unit_price' => $item['unit_price'] ?? 0,
                                'amount' => $item['amount'] ?? 0,
                            ]);
                            $total_amount += ($item['amount'] ?? 0);
                        }
                    }
                }

                if ($total_amount > 0) {
                    $expense->amount = $total_amount;
                    $expense->save();
                }

                return redirect()->route('erp-expenses.index', $type)->with('success', __('Expense successfully updated.'));
            }
            return redirect()->back()->with('error', __('Expense not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function destroy($type, $id)
    {
        if (Auth::user()->can('delete expense') || Auth::user()->type == 'company') {
            $expense = ErpExpense::find($id);
            if ($expense) {
                if ($expense->status == 'Approved' || $expense->status == 'Paid') {
                    return redirect()->back()->with('error', __('Cannot delete an approved or paid expense.'));
                }
                $expense->delete();
                return redirect()->route('erp-expenses.index', $type)->with('success', __('Expense successfully deleted.'));
            }
            return redirect()->back()->with('error', __('Expense not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function history()
    {
        if (Auth::user()->can('view office expense history') || Auth::user()->type == 'company') {
            $workspace_id = Auth::user()->currentWorkspace ?? 1;
            $expenses = ErpExpense::with(['category', 'employee', 'approver'])
                ->where('workspace_id', $workspace_id)
                ->whereIn('status', ['Approved', 'Paid', 'Rejected'])
                ->orderBy('id', 'desc')
                ->get();
            return view('erp_expenses.history', compact('expenses'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function approvals(Request $request)
    {
        if (Auth::user()->can('approve expense') || Auth::user()->type == 'company') {
            $workspace_id = Auth::user()->currentWorkspace ?? 1;

            // Fetch filter data
            $employees = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments = Department::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $types = [
                'purchase' => __('Purchase'),
                'convenience' => __('Convenience'),
                'utility' => __('Utility'),
                'salary' => __('Salary'),
                'history' => __('Office Expense History')
            ];
            $statuses = [
                'Pending Approval' => __('Pending Approval'),
                'Approved' => __('Approved'),
                'Rejected' => __('Rejected'),
                'Hold' => __('Hold'),
                'Paid' => __('Paid')
            ];

            // Build General Expenses Query
            $expensesQuery = ErpExpense::with(['category', 'employee', 'department', 'items'])
                ->where('workspace_id', $workspace_id);

            if ($request->filled('status')) {
                $expensesQuery->where('status', $request->status);
            }
            if ($request->filled('type')) {
                $expensesQuery->where('type', $request->type);
            }
            if ($request->filled('employee')) {
                $expensesQuery->where('employee_id', $request->employee);
            }
            if ($request->filled('department')) {
                $expensesQuery->where('department_id', $request->department);
            }
            if ($request->filled('date')) {
                $expensesQuery->whereDate('date', $request->date);
            }

            // Default sorting: Pending Approval first, then latest
            $expenses = $expensesQuery->orderByRaw("CASE WHEN status = 'Pending Approval' THEN 0 ELSE 1 END")
                ->latest()
                ->get();

            // Build Salary Query
            $salaryQuery = ErpSalarySheet::with(['employee'])
                ->where('workspace_id', $workspace_id);
            
            if ($request->filled('status')) {
                $salaryQuery->where('approval_status', $request->status == 'Pending Approval' ? 'Pending' : $request->status);
            } else {
                $salaryQuery->where('approval_status', 'Pending');
            }
            
            $salarySheets = $salaryQuery->latest()->get();

            return view('erp_expenses.approvals', compact('expenses', 'salarySheets', 'employees', 'departments', 'types', 'statuses'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function approve(Request $request, $id)
    {
        if (Auth::user()->can('approve expense') || Auth::user()->type == 'company') {
            DB::beginTransaction();
            try {
                $expense = ErpExpense::find($id);
                if ($expense) {
                    $expense->status = 'Approved';
                    $expense->approved_by = Auth::user()->id;
                    $expense->approved_at = now();
                    $expense->save();

                    if ($expense->erp_salary_sheet_id) {
                        $sheet = ErpSalarySheet::find($expense->erp_salary_sheet_id);
                        if ($sheet) {
                            $sheet->approval_status = 'Approved';
                            $sheet->approved_by = Auth::user()->id;
                            $sheet->save();
                        }
                    }

                    ErpExpenseStatusLog::create([
                        'erp_expense_id' => $expense->id,
                        'status' => 'Approved',
                        'comments' => $request->comments ?? 'Expense Approved by Admin',
                        'user_id' => Auth::user()->id,
                    ]);

                    $this->integrateWithAccounting($expense);

                    // Notify Creator
                    Notification::create([
                        'user_id' => $expense->created_by,
                        'type' => 'expense_approved',
                        'title' => 'Expense Status: Approved Successfully',
                        'message' => 'Your bill ' . $expense->serial_no . ' of ' . Auth::user()->priceFormat($expense->amount) . ' has been approved by Admin.',
                        'related_model' => 'ErpExpense',
                        'related_id' => $expense->id,
                        'created_by' => Auth::user()->id,
                        'is_read' => 0,
                    ]);

                    DB::commit();
                    return redirect()->back()->with('success', __('Expense Approved and synced with Accounting.'));
                }
                return redirect()->back()->with('error', __('Expense not found.'));
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Expense Approval Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Approval failed: ' . $e->getMessage());
            }
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function reject(Request $request, $id)
    {
        if (Auth::user()->can('approve expense') || Auth::user()->type == 'company') {
            DB::beginTransaction();
            try {
                $expense = ErpExpense::find($id);
                if ($expense) {
                    $expense->status = 'Rejected';
                    $expense->save();

                    ErpExpenseStatusLog::create([
                        'erp_expense_id' => $expense->id,
                        'status' => 'Rejected',
                        'comments' => $request->comments ?? 'Expense Rejected by Admin',
                        'user_id' => Auth::user()->id,
                    ]);

                    // Notify Creator
                    Notification::create([
                        'user_id' => $expense->created_by,
                        'type' => 'expense_rejected',
                        'title' => 'Expense Status: Rejected By Admin',
                        'message' => 'Your bill ' . $expense->serial_no . ' was rejected. Reason: ' . ($request->comments ?? 'N/A'),
                        'related_model' => 'ErpExpense',
                        'related_id' => $expense->id,
                        'created_by' => Auth::user()->id,
                        'is_read' => 0,
                    ]);

                    DB::commit();
                    return redirect()->back()->with('success', __('Expense Rejected.'));
                }
                return redirect()->back()->with('error', __('Expense not found.'));
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Expense Rejection Error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Rejection failed: ' . $e->getMessage());
            }
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function hold(Request $request, $id)
    {
        if (Auth::user()->can('approve expense') || Auth::user()->type == 'company') {
            $expense = ErpExpense::find($id);
            if ($expense) {
                $expense->status = 'Hold';
                $expense->save();

                ErpExpenseStatusLog::create([
                    'erp_expense_id' => $expense->id,
                    'status' => 'Hold',
                    'comments' => $request->comments ?? 'Expense Put on Hold by Admin',
                    'user_id' => Auth::user()->id,
                ]);

                // Notify Creator
                Notification::create([
                    'user_id' => $expense->created_by,
                    'type' => 'expense_on_hold',
                    'title' => 'Expense Status: Put On Hold',
                    'message' => 'Your bill ' . $expense->serial_no . ' has been put on hold for further review.',
                    'related_model' => 'ErpExpense',
                    'related_id' => $expense->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);

                return redirect()->back()->with('success', __('Expense put on hold.'));
            }
            return redirect()->back()->with('error', __('Expense not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function sendBack(Request $request, $id)
    {
        if (Auth::user()->can('approve expense') || Auth::user()->type == 'company') {
            $expense = ErpExpense::find($id);
            if ($expense) {
                $expense->status = 'Pending Approval'; // Or a specific 'Sent Back' status if you prefer
                $expense->save();

                ErpExpenseStatusLog::create([
                    'erp_expense_id' => $expense->id,
                    'status' => 'Sent Back',
                    'comments' => $request->comments ?? 'Expense Sent Back for Revision',
                    'user_id' => Auth::user()->id,
                ]);

                // Notify Creator
                Notification::create([
                    'user_id' => $expense->created_by,
                    'type' => 'expense_sent_back',
                    'title' => 'Expense Status: Sent Back for Revision',
                    'message' => 'Your bill ' . $expense->serial_no . ' has been sent back for revision. Reason: ' . ($request->comments ?? 'Check comments'),
                    'related_model' => 'ErpExpense',
                    'related_id' => $expense->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);

                return redirect()->back()->with('success', __('Expense sent back for revision.'));
            }
            return redirect()->back()->with('error', __('Expense not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    private function integrateWithAccounting($expense)
    {
        try {
            $bill = new Bill();
            $bill->bill_id = $this->billNumber();
            $bill->vender_id = $expense->employee_id ?? 0; 
            $bill->bill_date = $expense->date;
            $bill->due_date = $expense->date;
            $bill->status = 1; // Set to Sent (Approved) instead of Draft
            $bill->type = 'Expense';
            $bill->user_type = 'employee';
            $bill->created_by = $expense->created_by;
            $bill->category_id = 0; 
            $bill->order_number = 0;
            $bill->save();

            if ($expense->items->count() > 0) {
                foreach ($expense->items as $item) {
                    BillProduct::create([
                        'bill_id' => $bill->id,
                        'product_id' => 0,
                        'quantity' => $item->quantity,
                        'tax' => 0,
                        'discount' => 0,
                        'price' => $item->unit_price,
                        'description' => $item->product_name,
                    ]);
                }
            } else {
                BillProduct::create([
                    'bill_id' => $bill->id,
                    'product_id' => 0,
                    'quantity' => 1,
                    'tax' => 0,
                    'discount' => 0,
                    'price' => $expense->amount,
                    'description' => ($expense->category ? $expense->category->name : 'Expense') . ' - ' . $expense->description,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Accounting Integration Error: ' . $e->getMessage());
            throw $e; // Re-throw to be caught by approve() try-catch
        }
    }

    private function billNumber()
    {
        $latest = Bill::where('created_by', '=', Auth::user()->id)
            ->where('workspace', Auth::user()->currentWorkspace ?? 1)
            ->latest()->first();
        if (!$latest) {
            return 1;
        }
        return $latest->bill_id + 1;
    }

    public function print($type, $id)
    {
        if (Auth::user()->can('manage expense') || Auth::user()->type == 'company') {
            $expense = ErpExpense::with(['category', 'employee', 'items.unit', 'approver'])->find($id);
            if ($expense) {
                $setting = Utility::settings();
                return view('erp_expenses.print', compact('expense', 'type', 'setting'));
            }
            return redirect()->back()->with('error', __('Expense not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function getEmployeeInfo(Request $request)
    {
        $employee = Employee::with(['designation', 'department'])->find($request->employee_id);
        if ($employee) {
            return response()->json([
                'designation' => $employee->designation ? $employee->designation->name : '',
                'department' => $employee->department ? $employee->department->name : '',
                'phone' => $employee->phone ?? '',
            ]);
        }
        return response()->json(['error' => 'Not found'], 404);
    }

    public function categoryCreate($type)
    {
        return view('erp_expenses.category_create', compact('type'));
    }

    public function categoryStore(Request $request, $type)
    {
        $category = ErpExpenseCategory::create([
            'name' => $request->name,
            'module_type' => $type,
            'is_active' => true,
            'workspace_id' => Auth::user()->currentWorkspace ?? 1,
            'created_by' => Auth::user()->id,
        ]);

        return response()->json([
            'success' => true,
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    public function unitCreate()
    {
        return view('erp_expenses.unit_create');
    }

    public function unitStore(Request $request)
    {
        $unit = ErpExpenseUnit::create([
            'name' => $request->name,
            'slug' => strtolower(str_replace(' ', '-', $request->name)),
            'created_by' => Auth::user()->id,
            'status' => 1,
        ]);

        return response()->json([
            'success' => true,
            'id' => $unit->id,
            'name' => $unit->name,
        ]);
    }
}
