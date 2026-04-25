<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\TransportItem;
use App\Models\Client;
use App\Models\Utility;
use App\Models\Payable;
use App\Models\PayableItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage employee')) {
            $transports = Transport::where('created_by', '=', Auth::user()->creatorId())->get();
            return view('transport.index', compact('transports'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        $clients = Client::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        $clients = ['others' => __('Others')] + $clients;
        return view('transport.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'driver_name' => 'required',
                'contact_number' => 'required',
                'truck_number' => 'required',
                'starting_date' => 'required',
                'item_description' => 'required',
                'lc' => 'nullable|string',
                'ci' => 'nullable|string',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $transport = new Transport();
        $transport->unique_id = 'TR-' . time();
        $transport->client_id = $request->client_id == 'others' ? 0 : ($request->client_id ?? 0);
        $transport->manual_client_name = $request->manual_client_name;
        $transport->location_address = $request->location_address;
        $transport->location_lat = $request->location_lat;
        $transport->location_lng = $request->location_lng;
        
        $transport->driver_name = $request->driver_name;
        $transport->contact_number = $request->contact_number;
        $transport->truck_number = $request->truck_number;
        $transport->starting_date = $request->starting_date;
        $transport->item_description = $request->item_description;
        $transport->delivery_date = $request->delivery_date;
        $transport->lc = $request->lc;
        $transport->ci = $request->ci;
        
        $transport->status = 'pending';
        $transport->created_by = Auth::user()->creatorId();
        $transport->workspace = Auth::user()->workspace_id ?? 0;
        $transport->save();

        // Create a Payable record
        $payable = new Payable();
        $payable->unique_id = 'BILL-' . time();
        $payable->invoice_number = $transport->unique_id;
        $payable->date = date('Y-m-d');
        $payable->billing_direction = 'client'; // Assuming transport is usually for clients
        $payable->entity_id = $transport->client_id;
        $payable->billing_address = $transport->location_address;
        $payable->total_amount = 0; // Will be updated by Accounting
        $payable->status = 'due';
        $payable->created_by = Auth::user()->creatorId();
        $payable->save();

        $transport->payable_id = $payable->id;
        $transport->save();

        return redirect()->route('transports.index')->with('success', __('Transport record created successfully and bill sent to Accounting.'));
    }

    public function show(Transport $transport)
    {
        return view('transport.show', compact('transport'));
    }

    public function edit(Transport $transport)
    {
        $clients = Client::where('created_by', '=', Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        $clients = ['others' => __('Others')] + $clients;
        return view('transport.edit', compact('transport', 'clients'));
    }

    public function update(Request $request, Transport $transport)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'driver_name'      => 'required',
                'contact_number'   => 'required',
                'truck_number'     => 'required',
                'starting_date'    => 'required',
                'item_description' => 'required',
                'lc'               => 'nullable|string',
                'ci'               => 'nullable|string',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $transport->client_id        = $request->client_id == 'others' ? 0 : ($request->client_id ?? 0);
        $transport->manual_client_name = $request->manual_client_name;
        $transport->location_address = $request->location_address;
        $transport->location_lat     = $request->location_lat;
        $transport->location_lng     = $request->location_lng;
        $transport->driver_name      = $request->driver_name;
        $transport->contact_number   = $request->contact_number;
        $transport->truck_number     = $request->truck_number;
        $transport->starting_date    = $request->starting_date;
        $transport->item_description = $request->item_description;
        $transport->delivery_date    = $request->delivery_date;
        $transport->lc               = $request->lc;
        $transport->ci               = $request->ci;
        $transport->save();

        // Update linked payable address / entity if exists
        if ($transport->payable_id > 0) {
            $payable = Payable::find($transport->payable_id);
            if ($payable) {
                $payable->entity_id      = $transport->client_id;
                $payable->billing_address = $transport->location_address;
                $payable->save();
            }
        }

        return redirect()->route('transports.index')->with('success', __('Transport record updated successfully.'));
    }

    // Called every 30 seconds by Accounting dashboard via AJAX
    public function newBillCheck()
    {
        // Find any transport bills that are pending AND not yet seen
        $unseen = Transport::where('created_by', Auth::user()->creatorId())
            ->where('payable_id', '>', 0)
            ->where('status', 'pending')
            ->where('is_seen', false)
            ->get(['id', 'unique_id', 'driver_name', 'truck_number', 'manual_client_name', 'client_id']);

        $bills = $unseen->map(function ($t) {
            $client = $t->client_id > 0 ? optional($t->client)->name : $t->manual_client_name;
            return [
                'id'         => $t->id,
                'transport'  => $t->unique_id,
                'client'     => $client ?: '—',
                'truck'      => $t->truck_number,
            ];
        });

        return response()->json([
            'count'  => $unseen->count(),
            'bills'  => $bills,
        ]);
    }

    // Mark a transport bill as seen when Pay Bill is clicked
    public function markSeen($id)
    {
        $transport = Transport::where('id', $id)
            ->where('created_by', Auth::user()->creatorId())
            ->first();

        if ($transport) {
            $transport->is_seen = true;
            $transport->save();
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Transport $transport)
    {
        $transport->delete();
        return redirect()->route('transports.index')->with('success', __('Transport record deleted successfully.'));
    }
}
