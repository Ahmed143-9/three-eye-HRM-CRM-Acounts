<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $billings = Billing::where('created_by', Auth::user()->creatorId())->get();
            
            // Unpaid calculation
            $dueToClientTotal = $billings->where('type', 'due_to_client')->where('status', 'unpaid')->sum('amount');
            $dueToMeTotal = $billings->where('type', 'due_to_me')->where('status', 'unpaid')->sum('amount');
            
            // Paid calculation
            $myPaidTotal = $billings->where('type', 'due_to_client')->where('status', 'paid')->sum('amount');
            $clientPaidTotal = $billings->where('type', 'due_to_me')->where('status', 'paid')->sum('amount');

            return view('billing.index', compact('billings', 'dueToClientTotal', 'dueToMeTotal', 'myPaidTotal', 'clientPaidTotal'));
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

    public function destroy($id)
    {
        if (Auth::user()->can('manage bank account') || Auth::user()->type == 'company') {
            $billing = Billing::find($id);
            if ($billing->created_by == Auth::user()->creatorId()) {
                if ($billing->attachment) {
                    \Storage::delete($billing->attachment);
                }
                $billing->delete();
                return redirect()->route('billing.index')->with('success', __('Billing successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
