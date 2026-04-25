<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use App\Models\ReceivableItem;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\Consultant;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceivableController extends Controller
{
    public function index()
    {
        $receivables = Receivable::where('created_by', Auth::user()->creatorId())->get();
        return view('accounting.receivables.index', compact('receivables'));
    }

    public function create()
    {
        $lastId = Receivable::latest()->first();
        $nextId = $lastId ? $lastId->id + 1 : 1;
        $unique_id = 'REC-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        $creatorId = Auth::user()->creatorId();
        $suppliers = Supplier::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        $clients = Client::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        $consultants = Consultant::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();

        return view('accounting.receivables.create', compact('unique_id', 'suppliers', 'clients', 'consultants'));
    }

    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'unique_id' => 'required|unique:receivables',
                'invoice_number' => 'required',
                'date' => 'required|date',
                'billing_direction' => 'required',
                'entity_id' => 'required',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $receivable = new Receivable();
        $receivable->unique_id = $request->unique_id;
        $receivable->invoice_number = $request->invoice_number;
        $receivable->date = $request->date;
        $receivable->billing_direction = $request->billing_direction;
        $receivable->entity_id = $request->entity_id;
        $receivable->billing_address = $request->billing_address;
        $receivable->total_amount = $request->total_amount;
        $receivable->created_by = Auth::user()->creatorId();
        $receivable->save();

        $items = $request->items;
        foreach ($items as $item) {
            $receivableItem = new ReceivableItem();
            $receivableItem->receivable_id = $receivable->id;
            $receivableItem->serial = $item['serial'];
            $receivableItem->order_details = $item['order_details'];
            $receivableItem->qty = $item['qty'];
            $receivableItem->rate = $item['rate'];
            $receivableItem->amount = $item['amount'];
            $receivableItem->save();
        }

        return redirect()->route('receivables.index')->with('success', __('Receivable successfully created.'));
    }

    public function edit(Receivable $receivable)
    {
        $creatorId = Auth::user()->creatorId();
        $suppliers = Supplier::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        $clients = Client::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        $consultants = Consultant::where('created_by', $creatorId)->orWhere('created_by', Auth::user()->id)->get();
        
        return view('accounting.receivables.edit', compact('receivable', 'suppliers', 'clients', 'consultants'));
    }

    public function update(Request $request, Receivable $receivable)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'invoice_number' => 'required',
                'date' => 'required|date',
                'billing_direction' => 'required',
                'entity_id' => 'required',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $receivable->invoice_number = $request->invoice_number;
        $receivable->date = $request->date;
        $receivable->billing_direction = $request->billing_direction;
        $receivable->entity_id = $request->entity_id;
        $receivable->billing_address = $request->billing_address;
        $receivable->total_amount = $request->total_amount;
        $receivable->save();

        ReceivableItem::where('receivable_id', $receivable->id)->delete();

        $items = $request->items;
        foreach ($items as $item) {
            $receivableItem = new ReceivableItem();
            $receivableItem->receivable_id = $receivable->id;
            $receivableItem->serial = $item['serial'];
            $receivableItem->order_details = $item['order_details'];
            $receivableItem->qty = $item['qty'];
            $receivableItem->rate = $item['rate'];
            $receivableItem->amount = $item['amount'];
            $receivableItem->save();
        }

        return redirect()->route('receivables.index')->with('success', __('Receivable successfully updated.'));
    }

    public function destroy(Receivable $receivable)
    {
        $receivable->delete();
        return redirect()->route('receivables.index')->with('success', __('Receivable successfully deleted.'));
    }

}
