<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\TransportItem;
use App\Models\Payable;
use App\Models\PayableItem;
use App\Models\BillingPayment;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportBillController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage bill')) {
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
        if ($transport && Auth::user()->can('manage bill')) {
            $payable = Payable::find($transport->payable_id);
            return view('transport_bill.edit', compact('transport', 'payable'));
        } else {
            return redirect()->back()->with('error', __('Permission denied or record not found.'));
        }
    }

    public function update(Request $request, $id)
    {
        $transport = Transport::find($id);
        if (!$transport || !Auth::user()->can('manage bill')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $payable = Payable::find($transport->payable_id);
        if (!$payable) {
            return redirect()->back()->with('error', __('Payable record not found.'));
        }

        // Delete existing items
        PayableItem::where('payable_id', $payable->id)->delete();

        $total_amount = 0;
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                if (!empty($item['description']) && isset($item['amount']) && $item['amount'] !== '') {
                    $pItem               = new PayableItem();
                    $pItem->payable_id   = $payable->id;
                    $pItem->order_details = $item['description'];
                    $pItem->qty          = 1;
                    $pItem->rate         = (int) $item['amount'];
                    $pItem->amount       = (int) $item['amount'];
                    $pItem->save();
                    $total_amount += (int) $item['amount'];
                }
            }
        }

        // Update payable total and status
        $payable->total_amount = $total_amount;
        if ($total_amount > 0) {
            $payable->status = 'paid';
        }
        $payable->save();

        // ✅ Auto-mark as Paid in Accounting Ledger if amount > 0
        if ($total_amount > 0) {
            // Delete old auto-payments for this bill to avoid duplicates
            BillingPayment::where('billable_type', 'App\Models\Payable')
                ->where('billable_id', $payable->id)
                ->where('note', 'Auto-payment for Transport Bill')
                ->delete();

            // Create new payment record
            BillingPayment::create([
                'billable_type' => 'App\Models\Payable',
                'billable_id'   => $payable->id,
                'amount'        => $total_amount,
                'date'          => date('Y-m-d'),
                'payment_method' => 'Cash',
                'note'          => 'Auto-payment for Transport Bill',
                'created_by'    => Auth::user()->creatorId(),
            ]);
        }

        // ✅ Auto-set status: paid if amounts entered, pending if not
        $transport->status  = $total_amount > 0 ? 'paid' : 'pending';
        $transport->is_seen = true;   // Mark as seen once accountant saves
        $transport->save();

        return redirect()->route('transport.bill.index')
            ->with('success', __('Transport bill saved successfully. Status: ' . ucfirst($transport->status)));
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
