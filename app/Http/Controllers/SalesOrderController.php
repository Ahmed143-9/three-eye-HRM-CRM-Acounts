<?php

namespace App\Http\Controllers;
 
use App\Models\Client;
use App\Models\SalesCI;
use App\Models\SalesCITanker;
use App\Models\SalesConsignmentNote;
use App\Models\SalesLC;
use App\Models\SalesOrder;
use App\Models\SalesPI;
use App\Models\SalesPO;
use App\Models\SalesPOItem;
use App\Models\SalesPackingList;
use App\Models\SalesPackingListItem;
use App\Models\SalesWeightSlip;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index()
    {
        $orders = SalesOrder::where('created_by', Auth::user()->creatorId())->with('customer')->get();
        return view('sales_orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Client::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        return view('sales_orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $order = new SalesOrder();
        $order->order_number = 'ORD-' . time();
        $order->customer_id = $request->customer_id;
        $order->current_step = 'PO';
        $order->status = 'pending';
        $order->created_by = Auth::user()->creatorId();
        $order->save();

        return redirect()->route('sales-orders.show', $order->id)->with('success', __('Order created successfully.'));
    }

    public function show($id)
    {
        $order = SalesOrder::where('id', $id)->with(['po.items', 'pi', 'lc', 'ci.tankers', 'packingList.items', 'consignmentNote.weightSlips', 'customer'])->first();
        return view('sales_orders.show', compact('order'));
    }

    public function poStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        DB::transaction(function () use ($request, $order) {
            $po = SalesPO::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'client_name' => $request->client_name,
                    'client_address' => $request->client_address,
                    'client_email' => $request->client_email,
                    'client_phone' => $request->client_phone,
                    'grand_total' => $request->grand_total,
                    'signature' => $request->signature,
                    'created_by' => Auth::user()->creatorId(),
                ]
            );

            $po->items()->delete();
            foreach ($request->items as $item) {
                SalesPOItem::create([
                    'po_id' => $po->id,
                    'item_name' => $item['item'],
                    'description' => $item['description'],
                    'quantity' => $item['qty'],
                    'unit' => $item['unit'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            $order->current_step = 'PI';
            $order->save();
        });

        return redirect()->back()->with('success', __('PO saved successfully.'));
    }

    public function piStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        SalesPI::updateOrCreate(
            ['order_id' => $order->id],
            [
                'pi_number' => $request->pi_number,
                'pi_date' => $request->pi_date,
                'validity' => $request->validity,
                'lifting_time' => $request->lifting_time,
                'payment_terms' => $request->payment_terms,
                'hs_code' => $request->hs_code,
                'country_of_origin' => $request->country_of_origin,
                'tolerance' => $request->tolerance,
                'port_of_loading' => $request->port_of_loading,
                'port_of_discharge' => $request->port_of_discharge,
                'amount' => $request->amount,
                'created_by' => Auth::user()->creatorId(),
            ]
        );

        $order->current_step = 'LC';
        $order->save();

        return redirect()->back()->with('success', __('PI saved successfully.'));
    }

    public function lcStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        SalesLC::updateOrCreate(
            ['order_id' => $order->id],
            [
                'pi_id' => $order->pi->id,
                'lc_no' => $request->lc_no,
                'amount' => $request->amount,
                'lc_date' => $request->lc_date,
                'latest_shipment_date' => $request->latest_shipment_date,
                'lc_validity_date' => $request->lc_validity_date,
                'created_by' => Auth::user()->creatorId(),
            ]
        );

        $order->current_step = 'CI';
        $order->save();

        return redirect()->back()->with('success', __('LC saved successfully.'));
    }

    public function ciStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        DB::transaction(function () use ($request, $order) {
            $ci = SalesCI::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'pi_id' => $order->pi->id,
                    'lc_id' => $order->lc->id,
                    'ci_number' => $request->ci_number,
                    'ci_date' => $request->ci_date,
                    'lc_validity_date' => $request->lc_validity_date,
                    'latest_shipment_date' => $request->latest_shipment_date,
                    'created_by' => Auth::user()->creatorId(),
                ]
            );

            $ci->tankers()->delete();
            foreach ($request->tankers as $tanker) {
                SalesCITanker::create([
                    'ci_id' => $ci->id,
                    'tanker_number' => $tanker['tanker_number'],
                    'quantity_mt' => $tanker['qty_mt'],
                    'cpt_usd' => $tanker['cpt_usd'],
                    'total_amount_usd' => $tanker['total_amount'],
                ]);
            }

            $order->current_step = 'Packing List';
            $order->save();
        });

        return redirect()->back()->with('success', __('CI saved successfully.'));
    }

    public function plStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        if ($request->hasFile('file')) {
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $request->file->storeAs('public/sales_orders', $fileName);
            $filePath = 'storage/sales_orders/' . $fileName;
        }

        SalesPackingList::updateOrCreate(
            ['order_id' => $order->id],
            [
                'file_path' => $filePath ?? null,
                'created_by' => Auth::user()->creatorId(),
            ]
        );

        $order->current_step = 'Consignment Note';
        $order->save();

        return redirect()->back()->with('success', __('Packing List saved successfully.'));
    }

    public function cnStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        DB::transaction(function () use ($request, $order) {
            if ($request->hasFile('file')) {
                $fileName = time() . '_cn_' . $request->file->getClientOriginalName();
                $request->file->storeAs('public/sales_orders', $fileName);
                $filePath = 'storage/sales_orders/' . $fileName;
            }

            $cn = SalesConsignmentNote::updateOrCreate(
                ['order_id' => $order->id],
                ['file_path' => $filePath ?? null]
            );

            $cn->weightSlips()->delete();
            foreach ($request->weight_slips as $slip) {
                SalesWeightSlip::create([
                    'consignment_note_id' => $cn->id,
                    'tanker_id' => $slip['tanker_id'],
                    'in_out_number' => $slip['in_out_number'],
                    'gross_weight' => $slip['gross'],
                    'tare_weight' => $slip['tare'],
                    'net_weight' => $slip['net'],
                ]);
            }

            $order->current_step = 'TR';
            $order->save();
        });

        return redirect()->back()->with('success', __('Consignment Note saved successfully.'));
    }
}
