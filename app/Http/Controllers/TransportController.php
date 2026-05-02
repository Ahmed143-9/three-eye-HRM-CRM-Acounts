<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\TransportItem;
use App\Models\Client;
use App\Models\Utility;
use App\Models\Payable;
use App\Models\PayableItem;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage employee')) {
            $transports = Transport::where('created_by', '=', Auth::user()->creatorId())->get();
            
            // Fetch finalized sales orders that haven't been linked to a transport yet
            $pendingOrders = SalesOrder::where('status', 'finalized')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('transports')
                        ->whereRaw('transports.sales_order_id = sales_orders.id');
                })
                ->where('created_by', Auth::user()->creatorId())
                ->get();

            return view('transport.index', compact('transports', 'pendingOrders'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create(Request $request)
    {
        $creatorId = Auth::user()->creatorId();
        $clients = Client::where('created_by', $creatorId)
            ->orWhere('id', '>', 0) 
            ->get()->pluck('name', 'id')->toArray();
        $clients = ['others' => __('Others')] + $clients;
        
        $salesOrder = null;
        $activeCi = null;
        if ($request->has('sales_order_id')) {
            $salesOrder = SalesOrder::with(['customer', 'lc', 'cis.tankers', 'cis.delivery', 'delivery', 'po.items'])->find($request->sales_order_id);
            if ($request->has('ci_id')) {
                $activeCi = $salesOrder ? $salesOrder->cis->find($request->ci_id) : null;
            }
        }

        return view('transport.create', compact('clients', 'salesOrder', 'activeCi'));
    }

    public function store(Request $request)
    {
        $transport = new Transport();
        $transport->unique_id = 'TR-' . time();
        $transport->sales_order_id = $request->sales_order_id;
        $transport->ci_id          = $request->ci_id;
        $transport->client_id      = $request->client_id == 'others' ? 0 : ($request->client_id ?? 0);
        $transport->manual_client_name = $request->manual_client_name;
        $transport->location_address = $request->location_address;
        $transport->location_lat     = $request->location_lat;
        $transport->location_lng     = $request->location_lng;
        $drivers = $request->drivers;
        $firstDriver = is_array($drivers) ? reset($drivers) : null;
        $transport->driver_name      = $firstDriver['name'] ?? null;
        $transport->contact_number   = $firstDriver['contact'] ?? null;
        $transport->truck_number     = $firstDriver['truck_number'] ?? null;
        $transport->starting_date    = $firstDriver['starting_date'] ?? null;
        $transport->delivery_date    = $firstDriver['delivery_date'] ?? null;
        $transport->drivers_data     = json_encode($request->drivers);
        $transport->transport_type   = $request->transport_type;
        $transport->required_trucks  = $request->required_trucks;
        
        $transport->item_description = $request->item_description;
        
        // Auto-fill from Sales Order if present and not manually overridden
        if ($request->sales_order_id) {
            $order = SalesOrder::with(['lc', 'cis'])->find($request->sales_order_id);
            $transport->lc = $request->lc ?? (optional($order->lc)->client_lc_no);
            
            if ($request->ci_id) {
                $ci_rec = $order->cis->find($request->ci_id);
                $transport->ci = $request->ci ?? (optional($ci_rec)->ci_number);
            } else {
                $transport->ci = $request->ci;
            }
        } else {
            $transport->lc = $request->lc;
            $transport->ci = $request->ci;
        }

        $transport->status           = 'pending';
        $transport->is_seen          = false;
        $transport->created_by       = Auth::user()->creatorId();
        $transport->workspace        = 0; // Fixed null error
        $transport->save();

        // 🚀 AUTO-CREATE PAYABLE FOR ACCOUNTANT
        $payable = Payable::create([
            'unique_id'         => 'BILL-' . time(),
            'invoice_number'    => 'TR-' . $transport->id,
            'date'              => $transport->starting_date ?? date('Y-m-d'),
            'billing_direction' => 'client',
            'entity_id'         => $transport->client_id > 0 ? $transport->client_id : 0,
            'sales_order_id'    => $transport->sales_order_id,
            'ci_id'             => $transport->ci_id,
            'billing_address'   => $transport->location_address,
            'total_amount'      => 0, // Accountant will fill this
            'status'            => 'due',
            'created_by'        => Auth::user()->creatorId(),
        ]);

        $transport->payable_id = $payable->id;
        $transport->save();

        return redirect()->route('transports.index')->with('success', __('Transport record created successfully.'));
    }

    public function show(Transport $transport)
    {
        return view('transport.show', compact('transport'));
    }

    public function edit(Transport $transport)
    {
        $creatorId = Auth::user()->creatorId();
        $clients = Client::where('created_by', $creatorId)
            ->orWhere('id', '>', 0)
            ->get()->pluck('name', 'id')->toArray();
        $clients = ['others' => __('Others')] + $clients;
        return view('transport.edit', compact('transport', 'clients'));
    }

    public function update(Request $request, Transport $transport)
    {
        $transport->client_id = $request->client_id == 'others' ? 0 : ($request->client_id ?? 0);
        $transport->manual_client_name = $request->manual_client_name;
        $transport->location_address = $request->location_address;
        $transport->location_lat     = $request->location_lat;
        $transport->location_lng     = $request->location_lng;
        $drivers = $request->drivers;
        $firstDriver = is_array($drivers) ? reset($drivers) : null;
        $transport->driver_name      = $firstDriver['name'] ?? null;
        $transport->contact_number   = $firstDriver['contact'] ?? null;
        $transport->truck_number     = $firstDriver['truck_number'] ?? null;
        $transport->starting_date    = $firstDriver['starting_date'] ?? null;
        $transport->delivery_date    = $firstDriver['delivery_date'] ?? null;
        $transport->drivers_data     = json_encode($request->drivers);
        $transport->transport_type   = $request->transport_type;
        $transport->required_trucks  = $request->required_trucks;

        $transport->item_description = $request->item_description;
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
        // No popups for admin/company
        if (Auth::user()->type == 'admin' || Auth::user()->type == 'company') {
            return response()->json(['count' => 0, 'bills' => []]);
        }

        // Find any transport bills that are pending AND not yet seen
        $unseen = Transport::with('salesOrder')->where('created_by', Auth::user()->creatorId())
            ->where('payable_id', '>', 0)
            ->where('status', 'pending')
            ->where('is_seen', false)
            ->get(['id', 'unique_id', 'driver_name', 'truck_number', 'manual_client_name', 'client_id', 'sales_order_id']);

        $bills = $unseen->map(function ($t) {
            $client = $t->client_id > 0 ? optional($t->client)->name : $t->manual_client_name;
            return [
                'id'         => $t->id,
                'transport'  => $t->unique_id,
                'order'      => $t->salesOrder ? $t->salesOrder->order_number : '—',
                'client'     => $client ?: '—',
                'truck'      => $t->truck_number,
            ];
        });

        return response()->json([
            'count'  => $unseen->count(),
            'bills'  => $bills,
        ]);
    }

    // Called by HRM dashboard via AJAX to check for new finalized sales orders
    public function transportRequestCheck()
    {
        // No popups for admin/company
        if (Auth::user()->type == 'admin' || Auth::user()->type == 'company') {
            return response()->json(['count' => 0, 'orders' => []]);
        }

        $pendingOrders = SalesOrder::where('status', 'finalized')
            ->where('is_transport_notified', false)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('transports')
                    ->whereRaw('transports.sales_order_id = sales_orders.id');
            })
            ->where('created_by', Auth::user()->creatorId())
            ->get(['id', 'order_number', 'customer_id']);

        $orders = $pendingOrders->map(function ($o) {
            return [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer' => optional($o->customer)->name ?? '—',
            ];
        });

        return response()->json([
            'count' => $pendingOrders->count(),
            'orders' => $orders,
        ]);
    }

    // Mark transport bills as seen
    public function markBillSeen(Request $request)
    {
        if ($request->has('ids')) {
            Transport::whereIn('id', $request->ids)->update(['is_seen' => true]);
        }
        return response()->json(['success' => true]);
    }

    // Mark sales orders as notified for transport
    public function markRequestSeen(Request $request)
    {
        if ($request->has('ids')) {
            SalesOrder::whereIn('id', $request->ids)->update(['is_transport_notified' => true]);
        }
        return response()->json(['success' => true]);
    }

    // Legacy markSeen for single ID if needed
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
