<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\TransportItem;
use App\Models\Payable;
use App\Models\PayableItem;
use App\Models\Receivable;
use App\Models\ReceivableItem;
use App\Models\BillingPayment;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportBillController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('Manage Purchases & Suppliers')) {
            $bills = Transport::where('created_by', '=', Auth::user()->creatorId())
                ->where('payable_id', '>', 0)
                ->get();
            return view('transport_bill.index', compact('bills'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        $transport = Transport::find($id);
        if ($transport && Auth::user()->can('Manage Purchases & Suppliers')) {
            $payable = Payable::find($transport->payable_id);
            $receivable = Receivable::where('transport_id', $transport->id)->first();
            return view('transport_bill.edit', compact('transport', 'payable', 'receivable'));
        } else {
            return redirect()->back()->with('error', __('Permission denied or record not found.'));
        }
    }

    public function update(Request $request, $id)
    {
        $transport = Transport::find($id);
        if (!$transport || !Auth::user()->can('Manage Purchases & Suppliers')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $payable = Payable::find($transport->payable_id);
        $receivable = Receivable::where('transport_id', $transport->id)->first();

        if (!$payable) {
            return redirect()->back()->with('error', __('Payable record not found.'));
        }

        DB::beginTransaction();
        try {
            // 1. UPDATE PAYABLE (Cost we pay to transporter)
            PayableItem::where('payable_id', $payable->id)->delete();
            $total_payable = 0;
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    if (!empty($item['description']) && isset($item['amount']) && $item['amount'] !== '') {
                        PayableItem::create([
                            'payable_id'   => $payable->id,
                            'order_details' => $item['description'],
                            'qty'          => 1,
                            'rate'         => (int) $item['amount'],
                            'amount'       => (int) $item['amount'],
                        ]);
                        $total_payable += (int) $item['amount'];
                    }
                }
            }
            $payable->total_amount = $total_payable;
            $payable->status = $total_payable > 0 ? 'paid' : 'due';
            $payable->save();

            // Auto-payment for Payable
            if ($total_payable > 0) {
                BillingPayment::updateOrCreate(
                    ['billable_type' => 'App\Models\Payable', 'billable_id' => $payable->id, 'note' => 'Auto-payment for Transport Bill'],
                    ['amount' => $total_payable, 'date' => date('Y-m-d'), 'payment_method' => 'Cash', 'created_by' => Auth::user()->creatorId()]
                );
            }

            // 2. UPDATE RECEIVABLE (Income from Client)
            if ($receivable) {
                ReceivableItem::where('receivable_id', $receivable->id)->delete();
                $total_receivable = 0;
                if ($request->has('receivable_items')) {
                    foreach ($request->receivable_items as $rItem) {
                        if (!empty($rItem['description']) && isset($rItem['amount']) && $rItem['amount'] !== '') {
                            ReceivableItem::create([
                                'receivable_id' => $receivable->id,
                                'order_details' => $rItem['description'],
                                'qty'           => 1,
                                'rate'          => (int) $rItem['amount'],
                                'amount'        => (int) $rItem['amount'],
                            ]);
                            $total_receivable += (int) $rItem['amount'];
                        }
                    }
                }
                $receivable->total_amount = $total_receivable;
                $receivable->status = $total_receivable > 0 ? 'due' : 'due'; // Keep as due since client hasn't paid us yet
                $receivable->save();
            }

            // Update Transport Status
            $transport->status  = $total_payable > 0 ? 'paid' : 'pending';
            $transport->is_seen = true;
            $transport->save();

            DB::commit();
            return redirect()->route('transport.bill.index')->with('success', __('Transport bill and client receivable saved successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('Error saving bill: ') . $e->getMessage());
        }
    }

    public function pay($id)
    {
        $transport = Transport::find($id);
        if ($transport) {
            return redirect()->route('transports.show', $transport->id);
        }
        return redirect()->back()->with('error', __('Record not found.'));
    }
}
