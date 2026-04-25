<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Payable;
use App\Models\Receivable;
use App\Models\BillingPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $creatorId = Auth::user()->creatorId();
            
            $payablesQuery = Payable::where('created_by', $creatorId);
            $receivablesQuery = Receivable::where('created_by', $creatorId);

            // Fetch totals before filtering for summary boxes
            // These should be REMAINING dues (Total - Paid)
            $allPayables = (clone $payablesQuery)->get();
            $allReceivables = (clone $receivablesQuery)->get();

            $totalMyDue = 0;
            foreach($allPayables as $p) {
                $totalMyDue += $p->getDueAmount();
            }

            $totalOthersDue = 0;
            foreach($allReceivables as $r) {
                $totalOthersDue += $r->getDueAmount();
            }

            // Apply Filters (Note: these filters might need to be adjusted for dynamic values)
            if ($request->has('type') && !empty($request->type)) {
                if ($request->type == 'payable') {
                    $receivablesQuery->whereRaw('1=0');
                } elseif ($request->type == 'receivable') {
                    $payablesQuery->whereRaw('1=0');
                }
            }

            $payables = $payablesQuery->get();
            $receivables = $receivablesQuery->get();

            // Merge for table
            $billings = collect();
            
            foreach ($payables as $payable) {
                $payable->billing_type = 'Payable';
                $payable->source = 'Payable Module';
                $payable->paid_amount = $payable->getTotalPaid();
                $payable->adjustment_amount = $payable->getTotalAdjustment();
                $payable->due_amount = $payable->getDueAmount();
                $payable->current_status = $payable->getCalculatedStatus();
                
                // Reminder Logic
                $payable->reminder_status = null;
                if ($payable->due_amount > 0 && $payable->next_due_date) {
                    $dueDate = \Carbon\Carbon::parse($payable->next_due_date);
                    if ($dueDate->isPast() && !$dueDate->isToday()) {
                        $payable->reminder_status = 'overdue';
                    } elseif ($dueDate->isToday() || $dueDate->isTomorrow()) {
                        $payable->reminder_status = 'due_soon';
                    }
                }
                $billings->push($payable);
            }

            foreach ($receivables as $receivable) {
                $receivable->billing_type = 'Receivable';
                $receivable->source = 'Receivable Module';
                $receivable->paid_amount = $receivable->getTotalPaid();
                $receivable->adjustment_amount = $receivable->getTotalAdjustment();
                $receivable->due_amount = $receivable->getDueAmount();
                $receivable->current_status = $receivable->getCalculatedStatus();

                // Reminder Logic
                $receivable->reminder_status = null;
                if ($receivable->due_amount > 0 && $receivable->next_due_date) {
                    $dueDate = \Carbon\Carbon::parse($receivable->next_due_date);
                    if ($dueDate->isPast() && !$dueDate->isToday()) {
                        $receivable->reminder_status = 'overdue';
                    } elseif ($dueDate->isToday() || $dueDate->isTomorrow()) {
                        $receivable->reminder_status = 'due_soon';
                    }
                }
                $billings->push($receivable);
            }

            // Filter by status if requested
            if ($request->has('status') && !empty($request->status)) {
                $billings = $billings->filter(function($item) use ($request) {
                    return $item->current_status == $request->status;
                });
            }

            // Sort by date descending
            $billings = $billings->sortByDesc('date');

            return view('billing.index', compact('billings', 'totalMyDue', 'totalOthersDue'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            return view('billing.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $validator = \Validator::make(
                $request->all(), [
                    'amount' => 'required|numeric',
                    'type' => 'required|in:due_to_client,due_to_me',
                    'status' => 'required|in:paid,unpaid',
                    'from_date' => 'nullable|date',
                    'to_date' => 'nullable|date',
                    'attachment' => 'nullable|mimes:jpeg,png,jpg,pdf,doc,docx|max:20480',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $billing = new Billing();
            $billing->date = $request->date ?? date('Y-m-d');
            $billing->amount = $request->amount;
            $billing->type = $request->type;
            $billing->status = $request->status;
            
            // from and to fields
            $billing->from_date = $request->from_date;
            $billing->from_bank_name = $request->from_bank_name;
            $billing->from_bank_number = $request->from_bank_number;
            $billing->to_date = $request->to_date;
            $billing->to_bank_name = $request->to_bank_name;
            $billing->to_bank_number = $request->to_bank_number;
            $billing->description = $request->description;
            
            $billing->created_by = Auth::user()->creatorId();

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $dir = 'uploads/billing/';
                $path = \App\Models\Utility::upload_file($request, 'attachment', $filename, $dir, []);
                if ($path['flag'] == 1) {
                    $billing->attachment = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $billing->save();

            return redirect()->route('billing.index')->with('success', __('Billing successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $billing = Billing::find($id);
            if ($billing->created_by == Auth::user()->creatorId()) {
                return view('billing.edit', compact('billing'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $billing = Billing::find($id);
            if ($billing->created_by == Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(), [
                        'amount' => 'required|numeric',
                        'type' => 'required|in:due_to_client,due_to_me',
                        'status' => 'required|in:paid,unpaid',
                        'from_date' => 'nullable|date',
                        'to_date' => 'nullable|date',
                        'attachment' => 'nullable|mimes:jpeg,png,jpg,pdf,doc,docx|max:20480',
                    ]
                );

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $billing->date = $request->date ?? date('Y-m-d');
                $billing->amount = $request->amount;
                $billing->type = $request->type;
                $billing->status = $request->status;

                $billing->from_date = $request->from_date;
                $billing->from_bank_name = $request->from_bank_name;
                $billing->from_bank_number = $request->from_bank_number;
                $billing->to_date = $request->to_date;
                $billing->to_bank_name = $request->to_bank_name;
                $billing->to_bank_number = $request->to_bank_number;
                $billing->description = $request->description;

                if ($request->hasFile('attachment')) {
                    // remove old file if necessary (optional)
                    if ($billing->attachment) {
                        \Storage::delete($billing->attachment);
                    }

                    $file = $request->file('attachment');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $dir = 'uploads/billing/';
                    $path = \App\Models\Utility::upload_file($request, 'attachment', $filename, $dir, []);
                    if ($path['flag'] == 1) {
                        $billing->attachment = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }

                $billing->save();

                return redirect()->route('billing.index')->with('success', __('Billing successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function addPayment($type, $id)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $billable = ($type == 'Payable') ? Payable::find($id) : Receivable::find($id);
            
            if ($billable) {
                return view('billing.add_payment', compact('billable', 'type'));
            } else {
                return response()->json(['error' => __('Bill not found.')], 404);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function storePayment(Request $request, $type, $id)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $billable = ($type == 'Payable') ? Payable::find($id) : Receivable::find($id);
            
            if ($billable) {
                $validator = \Validator::make(
                    $request->all(), [
                        'amount' => 'required|numeric|min:0',
                        'adjustment_amount' => 'nullable|numeric|min:0',
                        'date' => 'required|date',
                        'next_due_date' => 'nullable|date|after_or_equal:date',
                    ]
                );

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                // Total allocated in this entry
                $allocated = (float)$request->amount + (float)$request->adjustment_amount;
                if ($allocated <= 0) {
                    return redirect()->back()->with('error', __('Either Payment Amount or Adjustment Amount must be greater than 0.'));
                }

                // Create payment entry
                $payment = new BillingPayment();
                $payment->billable_type = ($type == 'Payable') ? Payable::class : Receivable::class;
                $payment->billable_id = $id;
                $payment->amount = $request->amount;
                $payment->adjustment_amount = $request->adjustment_amount ?? 0;
                $payment->adjustment_reason = $request->adjustment_reason;
                $payment->date = $request->date;
                $payment->payment_method = $request->payment_method;
                $payment->note = $request->note;
                $payment->next_due_date = $request->next_due_date;
                $payment->created_by = Auth::user()->creatorId();
                $payment->save();

                // Update billable's next due date
                if ($request->next_due_date) {
                    $billable->next_due_date = $request->next_due_date;
                }
                
                $billable->save();

                return redirect()->route('billing.index')->with('success', __('Payment/Adjustment successfully added.'));
            } else {
                return redirect()->back()->with('error', __('Bill not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function showPayment($type, $id)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $billable = ($type == 'Payable') ? Payable::find($id) : Receivable::find($id);
            
            if ($billable) {
                $payments = $billable->payments()->orderBy('date', 'asc')->get();
                return view('billing.view_payments', compact('billable', 'type', 'payments'));
            } else {
                return response()->json(['error' => __('Bill not found.')], 404);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function editPayment($paymentId)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $payment = BillingPayment::find($paymentId);
            if ($payment) {
                return view('billing.edit_payment', compact('payment'));
            }
            return response()->json(['error' => __('Payment not found.')], 404);
        }
        return response()->json(['error' => __('Permission denied.')], 401);
    }

    public function updatePayment(Request $request, $paymentId)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $payment = BillingPayment::find($paymentId);
            if (!$payment) {
                return redirect()->back()->with('error', __('Payment not found.'));
            }

            $validator = \Validator::make($request->all(), [
                'amount'            => 'required|numeric|min:0',
                'adjustment_amount' => 'nullable|numeric|min:0',
                'date'              => 'required|date',
                'next_due_date'     => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $allocated = (float)$request->amount + (float)$request->adjustment_amount;
            if ($allocated <= 0) {
                return redirect()->back()->with('error', __('Payment Amount or Adjustment must be greater than 0.'));
            }

            $payment->amount            = $request->amount;
            $payment->adjustment_amount = $request->adjustment_amount ?? 0;
            $payment->adjustment_reason = $request->adjustment_reason;
            $payment->date              = $request->date;
            $payment->payment_method    = $request->payment_method;
            $payment->note              = $request->note;
            $payment->next_due_date     = $request->next_due_date;
            $payment->save();

            // Sync next_due_date on the parent billable
            $billable = $payment->billable;
            if ($request->next_due_date && $billable) {
                $billable->next_due_date = $request->next_due_date;
                $billable->save();
            }

            return redirect()->route('billing.index')->with('success', __('Payment entry updated successfully.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    public function destroyPayment($paymentId)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $payment = BillingPayment::find($paymentId);
            if ($payment) {
                $payment->delete();
                return redirect()->route('billing.index')->with('success', __('Payment entry deleted.'));
            }
            return redirect()->back()->with('error', __('Payment not found.'));
        }
        return redirect()->back()->with('error', __('Permission denied.'));
    }
}
