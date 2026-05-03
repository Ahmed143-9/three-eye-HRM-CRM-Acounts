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
use Illuminate\Support\Facades\Schema;

class ErpExpenseController extends Controller
{
    /** Statuses on which an approver may act */
    private const APPROVABLE_STATUSES = ['Pending Approval', 'Hold', 'Sent Back'];
    private static array $erpExpenseColumnCache = [];

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

                $this->notifyApproversExpenseSubmitted($expense, $type);

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

                if ($expense->status === 'Sent Back') {
                    $this->updateExpenseFields($expense, [
                        'status' => 'Pending Approval',
                        'send_back_reason' => null,
                    ]);
                    ErpExpenseStatusLog::create([
                        'erp_expense_id' => $expense->id,
                        'status' => 'Pending Approval',
                        'comments' => __('Expense resubmitted for approval after corrections'),
                        'user_id' => Auth::user()->id,
                    ]);
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
                ->whereIn('status', ['Approved', 'Processing Payment', 'Paid', 'Rejected'])
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
                'Processing Payment' => __('Processing Payment'),
                'Rejected' => __('Rejected'),
                'Hold' => __('Hold'),
                'Sent Back' => __('Sent Back'),
                'Paid' => __('Paid'),
            ];

            // Build General Expenses Query
            $expensesQuery = ErpExpense::with(['category', 'employee', 'department', 'items'])
                ->where('workspace_id', $workspace_id);

            if ($request->filled('status')) {
                $expensesQuery->where('status', $request->status);
            } elseif (! $request->boolean('show_all')) {
                $expensesQuery->whereIn('status', self::APPROVABLE_STATUSES);
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

            $expenses = $expensesQuery->orderByRaw("FIELD(status, 'Pending Approval', 'Sent Back', 'Hold', 'Approved', 'Processing Payment', 'Rejected', 'Paid')")
                ->latest('id')
                ->paginate(25, ['*'], 'expense_page')
                ->appends($request->query());

            // Build Salary Query
            $salaryQuery = ErpSalarySheet::with(['employee'])
                ->where('workspace_id', $workspace_id);
            
            if ($request->filled('status')) {
                $salaryQuery->where('approval_status', $request->status == 'Pending Approval' ? 'Pending' : $request->status);
            } else {
                $salaryQuery->where('approval_status', 'Pending');
            }
            
            $salarySheets = $salaryQuery->latest()->paginate(25, ['*'], 'salary_page')->appends($request->query());

            return view('erp_expenses.approvals', compact('expenses', 'salarySheets', 'employees', 'departments', 'types', 'statuses'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function approve(Request $request, $id)
    {
        if (! Auth::user()->can('approve expense') && Auth::user()->type != 'company') {
            return $this->approvalErrorResponse($request, __('Permission denied.'), 403);
        }

        $workspace_id = Auth::user()->currentWorkspace ?? 1;
        $expense = ErpExpense::with(['items', 'category', 'employee', 'department'])->find($id);
        if (! $expense) {
            return $this->approvalErrorResponse($request, __('Expense not found.'));
        }
        if ($expense->workspace_id !== null && (int) $expense->workspace_id !== (int) $workspace_id) {
            return $this->approvalErrorResponse($request, __('Expense not found.'));
        }
        if (! in_array($expense->status, self::APPROVABLE_STATUSES, true)) {
            return $this->approvalErrorResponse($request, __('Status update failed: this expense cannot be approved in its current state.'));
        }

        DB::beginTransaction();
        try {
            $comment = $request->input('comments') ?: __('Expense approved by Admin');

            $this->updateExpenseFields($expense, [
                'status' => 'Approved',
                'approved_by' => Auth::user()->id,
                'approved_at' => now(),
                'payment_status' => 'Sent To Accounts',
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'hold_reason' => null,
                'send_back_reason' => null,
            ]);

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
                'comments' => $comment,
                'user_id' => Auth::user()->id,
            ]);

            $bill = $this->integrateWithAccounting($expense);

            Notification::create([
                'user_id' => $expense->created_by,
                'type' => 'expense_approved',
                'title' => __('Expense Status: Approved Successfully'),
                'message' => __('Your bill :serial of :amount has been approved.', [
                    'serial' => $expense->serial_no,
                    'amount' => Auth::user()->priceFormat($expense->amount),
                ]),
                'related_model' => 'ErpExpense',
                'related_id' => $expense->id,
                'created_by' => Auth::user()->id,
                'is_read' => 0,
            ]);

            $this->notifyAccountsExpenseReady($expense, $bill);

            DB::commit();

            return $this->approvalSuccessResponse($request, __('Expense approved. Accounting bill created and Accounts has been notified.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Expense Approval Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->approvalErrorResponse($request, __('Approval transaction failed: :msg', ['msg' => $e->getMessage()]));
        }
    }

    public function reject(Request $request, $id)
    {
        if (! Auth::user()->can('approve expense') && Auth::user()->type != 'company') {
            return $this->approvalErrorResponse($request, __('Permission denied.'), 403);
        }

        $workspace_id = Auth::user()->currentWorkspace ?? 1;
        $expense = ErpExpense::find($id);
        if (! $expense || ($expense->workspace_id !== null && (int) $expense->workspace_id !== (int) $workspace_id)) {
            return $this->approvalErrorResponse($request, __('Expense not found.'));
        }
        if (! in_array($expense->status, self::APPROVABLE_STATUSES, true)) {
            return $this->approvalErrorResponse($request, __('Status update failed: this expense cannot be rejected in its current state.'));
        }

        DB::beginTransaction();
        try {
            $reason = $request->input('comments') ?: __('No reason provided');

            $this->updateExpenseFields($expense, [
                'status' => 'Rejected',
                'rejected_by' => Auth::user()->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
                'approved_by' => null,
                'approved_at' => null,
                'hold_reason' => null,
                'send_back_reason' => null,
            ]);

            ErpExpenseStatusLog::create([
                'erp_expense_id' => $expense->id,
                'status' => 'Rejected',
                'comments' => $reason,
                'user_id' => Auth::user()->id,
            ]);

            Notification::create([
                'user_id' => $expense->created_by,
                'type' => 'expense_rejected',
                'title' => __('Expense Status: Rejected By Admin'),
                'message' => __('Your bill :serial was rejected. Reason: :reason', ['serial' => $expense->serial_no, 'reason' => $reason]),
                'related_model' => 'ErpExpense',
                'related_id' => $expense->id,
                'created_by' => Auth::user()->id,
                'is_read' => 0,
            ]);

            DB::commit();

            return $this->approvalSuccessResponse($request, __('Expense rejected. The creator has been notified.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Expense Rejection Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->approvalErrorResponse($request, __('Rejection failed: :msg', ['msg' => $e->getMessage()]));
        }
    }

    public function hold(Request $request, $id)
    {
        if (! Auth::user()->can('approve expense') && Auth::user()->type != 'company') {
            return $this->approvalErrorResponse($request, __('Permission denied.'), 403);
        }

        $workspace_id = Auth::user()->currentWorkspace ?? 1;
        $expense = ErpExpense::find($id);
        if (! $expense || ($expense->workspace_id !== null && (int) $expense->workspace_id !== (int) $workspace_id)) {
            return $this->approvalErrorResponse($request, __('Expense not found.'));
        }
        if (! in_array($expense->status, self::APPROVABLE_STATUSES, true)) {
            return $this->approvalErrorResponse($request, __('Status update failed: this expense cannot be put on hold in its current state.'));
        }

        DB::beginTransaction();
        try {
            $note = $request->input('comments') ?: __('Put on hold by Admin');

            $this->updateExpenseFields($expense, [
                'status' => 'Hold',
                'hold_reason' => $note,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'send_back_reason' => null,
            ]);

            ErpExpenseStatusLog::create([
                'erp_expense_id' => $expense->id,
                'status' => 'Hold',
                'comments' => $note,
                'user_id' => Auth::user()->id,
            ]);

            Notification::create([
                'user_id' => $expense->created_by,
                'type' => 'expense_on_hold',
                'title' => __('Expense Status: Put On Hold'),
                'message' => __('Your bill :serial has been put on hold for further review.', ['serial' => $expense->serial_no]),
                'related_model' => 'ErpExpense',
                'related_id' => $expense->id,
                'created_by' => Auth::user()->id,
                'is_read' => 0,
            ]);

            DB::commit();

            return $this->approvalSuccessResponse($request, __('Expense put on hold. The creator has been notified.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Expense Hold Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->approvalErrorResponse($request, __('Hold action failed: :msg', ['msg' => $e->getMessage()]));
        }
    }

    public function sendBack(Request $request, $id)
    {
        if (! Auth::user()->can('approve expense') && Auth::user()->type != 'company') {
            return $this->approvalErrorResponse($request, __('Permission denied.'), 403);
        }

        $workspace_id = Auth::user()->currentWorkspace ?? 1;
        $expense = ErpExpense::find($id);
        if (! $expense || ($expense->workspace_id !== null && (int) $expense->workspace_id !== (int) $workspace_id)) {
            return $this->approvalErrorResponse($request, __('Expense not found.'));
        }
        if (! in_array($expense->status, self::APPROVABLE_STATUSES, true)) {
            return $this->approvalErrorResponse($request, __('Status update failed: this expense cannot be sent back in its current state.'));
        }

        DB::beginTransaction();
        try {
            $reason = $request->input('comments') ?: __('Correction required');

            $this->updateExpenseFields($expense, [
                'status' => 'Sent Back',
                'send_back_reason' => $reason,
                'approved_by' => null,
                'approved_at' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'hold_reason' => null,
            ]);

            ErpExpenseStatusLog::create([
                'erp_expense_id' => $expense->id,
                'status' => 'Sent Back',
                'comments' => $reason,
                'user_id' => Auth::user()->id,
            ]);

            Notification::create([
                'user_id' => $expense->created_by,
                'type' => 'expense_sent_back',
                'title' => __('Expense Status: Sent Back for Revision'),
                'message' => __('Your bill :serial has been sent back for revision. Reason: :reason', ['serial' => $expense->serial_no, 'reason' => $reason]),
                'related_model' => 'ErpExpense',
                'related_id' => $expense->id,
                'created_by' => Auth::user()->id,
                'is_read' => 0,
            ]);

            DB::commit();

            return $this->approvalSuccessResponse($request, __('Expense sent back for revision. The creator has been notified.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Expense Send Back Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return $this->approvalErrorResponse($request, __('Send back failed: :msg', ['msg' => $e->getMessage()]));
        }
    }

    private function integrateWithAccounting(ErpExpense $expense): Bill
    {
        $expense->loadMissing(['items', 'category']);

        if ($expense->accounting_bill_id) {
            $existing = Bill::find($expense->accounting_bill_id);
            if ($existing) {
                return $existing;
            }
        }

        $ownerId = $this->tenantOwnerIdFromExpense($expense);

        $bill = new Bill();
        $bill->bill_id = (string) $this->nextBillSequenceForTenant($ownerId);
        $bill->vender_id = (int) ($expense->employee_id ?? 0);
        $bill->bill_date = $expense->date;
        $bill->due_date = $expense->date;
        $bill->status = 1;
        $bill->type = 'Bill';
        $bill->user_type = 'employee';
        $bill->created_by = $ownerId;
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
                'description' => ($expense->category ? $expense->category->name : 'Expense') . ' - ' . ($expense->description ?? ''),
            ]);
        }

        $expense->accounting_bill_id = $bill->id;
        $expense->save();

        return $bill;
    }

    private function tenantOwnerIdFromExpense(ErpExpense $expense): int
    {
        $creator = User::find($expense->created_by);

        return $creator ? (int) $creator->creatorId() : (int) Auth::user()->creatorId();
    }

    private function nextBillSequenceForTenant(int $ownerId): int
    {
        $latest = Bill::where('created_by', '=', $ownerId)->latest('id')->first();
        if (! $latest) {
            return 1;
        }

        return (int) $latest->bill_id + 1;
    }

    private function notifyApproversExpenseSubmitted(ErpExpense $expense, string $type): void
    {
        $recipientIds = User::where('type', 'company')->pluck('id');
        try {
            $recipientIds = $recipientIds->merge(User::permission('approve expense')->pluck('id'))->unique()->values();
        } catch (\Throwable $e) {
            \Log::warning('notifyApproversExpenseSubmitted permission lookup failed: ' . $e->getMessage());
        }

        foreach ($recipientIds as $userId) {
            try {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'expense_submitted',
                    'title' => ucfirst($type) . ' ' . __('Bill: Pending Approval'),
                    'message' => __('New :type expense of :amount submitted by :name.', [
                        'type' => $type,
                        'amount' => Auth::user()->priceFormat($expense->amount),
                        'name' => Auth::user()->name,
                    ]),
                    'related_model' => 'ErpExpense',
                    'related_id' => $expense->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Notification not sent (expense_submitted): ' . $e->getMessage());
            }
        }
    }

    private function notifyAccountsExpenseReady(ErpExpense $expense, Bill $bill): void
    {
        $recipientIds = collect();
        try {
            $recipientIds = User::permission('manage bill')->pluck('id')->unique()->values();
        } catch (\Throwable $e) {
            \Log::warning('notifyAccountsExpenseReady permission lookup failed: ' . $e->getMessage());
        }
        if ($recipientIds->isEmpty()) {
            $recipientIds = User::where('type', 'company')->pluck('id');
        }

        $dept = $expense->department ? $expense->department->name : '-';
        $emp = $expense->employee ? $expense->employee->name : '-';
        $approvedAt = $expense->approved_at ? $expense->approved_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i');

        foreach ($recipientIds as $userId) {
            try {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'expense_payment_ready',
                    'title' => __('New Approved Expense Ready For Payment'),
                    'message' => __('Expense Type: :type. Amount: :amount. Employee: :emp. Department: :dept. Approval Time: :at.', [
                        'type' => ucfirst($expense->type),
                        'amount' => Auth::user()->priceFormat($expense->amount),
                        'emp' => $emp,
                        'dept' => $dept,
                        'at' => $approvedAt,
                    ]),
                    'related_model' => 'Bill',
                    'related_id' => $bill->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Notification not sent (expense_payment_ready): ' . $e->getMessage());
            }
        }
    }

    private function approvalSuccessResponse(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }

    private function approvalErrorResponse(Request $request, string $message, int $status = 422)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return redirect()->back()->with('error', $message);
    }

    private function updateExpenseFields(ErpExpense $expense, array $fields): void
    {
        foreach ($fields as $field => $value) {
            if ($this->erpExpenseHasColumn($field)) {
                $expense->{$field} = $value;
            }
        }
        $expense->save();
    }

    private function erpExpenseHasColumn(string $column): bool
    {
        if (! array_key_exists($column, self::$erpExpenseColumnCache)) {
            self::$erpExpenseColumnCache[$column] = Schema::hasColumn('erp_expenses', $column);
        }

        return self::$erpExpenseColumnCache[$column];
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
    public function markAsPaid(Request $request, $id)
    {
        if (!Auth::user()->can('manage bill') && Auth::user()->type != 'company') {
            return $this->approvalErrorResponse($request, __('Permission denied.'), 403);
        }

        $expense = ErpExpense::find($id);
        if (!$expense) {
            return $this->approvalErrorResponse($request, __('Expense not found.'));
        }

        DB::beginTransaction();
        try {
            $comment = $request->input('comments') ?: __('Expense paid by Accountant');

            $this->updateExpenseFields($expense, [
                'status' => 'Paid',
                'payment_status' => 'Paid',
                'paid_by' => Auth::user()->id,
                'paid_at' => now(),
            ]);

            ErpExpenseStatusLog::create([
                'erp_expense_id' => $expense->id,
                'status' => 'Paid',
                'comments' => $comment,
                'user_id' => Auth::user()->id,
            ]);

            if ($expense->accounting_bill_id) {
                $bill = Bill::find($expense->accounting_bill_id);
                if ($bill) {
                    $bill->status = 4; // Set to Paid in Accounting
                    $bill->save();
                }
            }

            // Notify Admin
            $admins = User::where('type', 'company')->pluck('id');
            foreach ($admins as $adminId) {
                Notification::create([
                    'user_id' => $adminId,
                    'type' => 'expense_paid',
                    'title' => __('Expense Paid: ') . $expense->serial_no,
                    'message' => __('Expense of :amount has been marked as Paid by :name.', [
                        'amount' => Auth::user()->priceFormat($expense->amount),
                        'name' => Auth::user()->name,
                    ]),
                    'related_model' => 'ErpExpense',
                    'related_id' => $expense->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            }

            // Notify Creator
            if ($expense->created_by != Auth::user()->id) {
                Notification::create([
                    'user_id' => $expense->created_by,
                    'type' => 'expense_paid',
                    'title' => __('Your Bill is Paid'),
                    'message' => __('Your expense bill :serial of :amount has been paid.', [
                        'serial' => $expense->serial_no,
                        'amount' => Auth::user()->priceFormat($expense->amount),
                    ]),
                    'related_model' => 'ErpExpense',
                    'related_id' => $expense->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            }

            DB::commit();

            return $this->approvalSuccessResponse($request, __('Expense marked as paid successfully.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Expense Payment Error: ' . $e->getMessage());
            return $this->approvalErrorResponse($request, __('Payment update failed: ') . $e->getMessage());
        }
    }

    public function accountantReject(Request $request, $id)
    {
        if (!Auth::user()->can('manage bill') && Auth::user()->type != 'company') {
            return $this->approvalErrorResponse($request, __('Permission denied.'), 403);
        }

        $expense = ErpExpense::find($id);
        if (!$expense) {
            return $this->approvalErrorResponse($request, __('Expense not found.'));
        }

        DB::beginTransaction();
        try {
            $reason = $request->input('reason') ?: __('Rejected by Accounts team');

            $this->updateExpenseFields($expense, [
                'status' => 'Rejected by Accounts',
                'payment_status' => 'Rejected',
                'rejected_by' => Auth::user()->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            ErpExpenseStatusLog::create([
                'erp_expense_id' => $expense->id,
                'status' => 'Rejected by Accounts',
                'comments' => $reason,
                'user_id' => Auth::user()->id,
            ]);

            // Notify Admin
            $admins = User::where('type', 'company')->pluck('id');
            foreach ($admins as $adminId) {
                Notification::create([
                    'user_id' => $adminId,
                    'type' => 'expense_rejected_by_accounts',
                    'title' => __('Expense Rejected by Accounts'),
                    'message' => __('Expense :serial was rejected by Accounts. Reason: :reason', [
                        'serial' => $expense->serial_no,
                        'reason' => $reason,
                    ]),
                    'related_model' => 'ErpExpense',
                    'related_id' => $expense->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            }

            DB::commit();

            return $this->approvalSuccessResponse($request, __('Expense rejected by accounts. Admin has been notified.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->approvalErrorResponse($request, __('Rejection failed: ') . $e->getMessage());
        }
    }
}
