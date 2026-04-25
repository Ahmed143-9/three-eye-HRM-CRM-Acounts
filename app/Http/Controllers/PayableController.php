<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\PayableItem;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\Consultant;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayableController extends Controller
{
    public function index()
    {
        $payables = Payable::where('created_by', Auth::user()->creatorId())->get();
        return view('accounting.payables.index', compact('payables'));
    }

    public function create()
    {
        $lastId = Payable::latest()->first();
        $nextId = $lastId ? $lastId->id + 1 : 1;
        $unique_id = 'PAY-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        $creatorId = Auth::user()->creatorId();
        $suppliers = Supplier::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        $clients = Client::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        $consultants = Consultant::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();

        return view('accounting.payables.create', compact('unique_id', 'suppliers', 'clients', 'consultants'));
    }

    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'unique_id' => 'required|unique:payables',
                'invoice_number' => 'required',
                'date' => 'required|date',
                'billing_direction' => 'required',
                'entity_id' => 'required',
                'status' => 'required|in:unpaid,partial,paid',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $payable = new Payable();
        $payable->unique_id = $request->unique_id;
        $payable->invoice_number = $request->invoice_number;
        $payable->date = $request->date;
        $payable->billing_direction = $request->billing_direction;
        $payable->entity_id = $request->entity_id;
        $payable->billing_address = $request->billing_address;
        $payable->total_amount = $request->total_amount;
        $payable->status = $request->status;
        $payable->created_by = Auth::user()->creatorId();
        $payable->save();

        $items = $request->items;
        foreach ($items as $item) {
            $payableItem = new PayableItem();
            $payableItem->payable_id = $payable->id;
            $payableItem->serial = $item['serial'];
            $payableItem->order_details = $item['order_details'];
            $payableItem->qty = $item['qty'];
            $payableItem->rate = $item['rate'];
            $payableItem->amount = $item['amount'];
            $payableItem->save();
        }

        return redirect()->route('payables.index')->with('success', __('Payable successfully created.'));
    }

    public function edit(Payable $payable)
    {
        $creatorId = Auth::user()->creatorId();
        $suppliers = Supplier::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        $clients = Client::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        $consultants = Consultant::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        
        return view('accounting.payables.edit', compact('payable', 'suppliers', 'clients', 'consultants'));
    }

    public function update(Request $request, Payable $payable)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'invoice_number' => 'required',
                'date' => 'required|date',
                'billing_direction' => 'required',
                'entity_id' => 'required',
                'status' => 'required|in:unpaid,partial,paid',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $payable->invoice_number = $request->invoice_number;
        $payable->date = $request->date;
        $payable->billing_direction = $request->billing_direction;
        $payable->entity_id = $request->entity_id;
        $payable->billing_address = $request->billing_address;
        $payable->total_amount = $request->total_amount;
        $payable->status = $request->status;
        $payable->save();

        PayableItem::where('payable_id', $payable->id)->delete();

        $items = $request->items;
        foreach ($items as $item) {
            $payableItem = new PayableItem();
            $payableItem->payable_id = $payable->id;
            $payableItem->serial = $item['serial'];
            $payableItem->order_details = $item['order_details'];
            $payableItem->qty = $item['qty'];
            $payableItem->rate = $item['rate'];
            $payableItem->amount = $item['amount'];
            $payableItem->save();
        }

        return redirect()->route('payables.index')->with('success', __('Payable successfully updated.'));
    }

    public function destroy(Payable $payable)
    {
        $payable->delete();
        return redirect()->route('payables.index')->with('success', __('Payable successfully deleted.'));
    }

}
