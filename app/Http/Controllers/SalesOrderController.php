<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SalesCI;
use App\Models\SalesCITanker;
use App\Models\SalesConsignmentNote;
use App\Models\SalesLC;
use App\Models\SalesOrder;
use App\Models\SalesBuyingDetail;
use App\Models\SalesBuyingItem;
use App\Models\Payable;
use App\Models\PayableItem;
use App\Models\Receivable;
use App\Models\ReceivableItem;
use App\Models\Supplier;
use App\Models\SalesPI;
use App\Models\SalesPO;
use App\Models\SalesPOItem;
use App\Models\SalesPackingList;
use App\Models\SalesPackingListItem;
use App\Models\SalesWeightSlip;
use App\Models\Notification;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index()
    {
        $orders = SalesOrder::where('created_by', Auth::user()->creatorId())->with('customer')->orderBy('id', 'desc')->get();
        return view('sales_orders.index', compact('orders'));
    }

    public function fullReport($id)
    {
        $order = SalesOrder::where('id', $id)->with([
            'buying.items',
            'po.items',
            'pi',
            'lc',
            'cis.tankers',
            'cis.packingList',
            'cis.consignmentNote.weightSlips',
            'cis.delivery',
            'customer'
        ])->first();

        return view('sales_orders.print.full_report', compact('order'));
    }

    public function create()
    {
        // Get creator ID and allowed creator IDs
        $creatorId = Auth::user()->creatorId();

        $customers = Client::where('created_by', $creatorId)
            ->orWhere('id', '>', 0) // Broaden to ensure accessibility
            ->get()->pluck('name', 'id');

        return view('sales_orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $order = new SalesOrder();
        $order->order_number = 'ORD-' . time();
        $order->customer_id = $request->customer_id;
        $order->current_step = 'Buying';
        $order->status = 'pending';
        $order->created_by = Auth::user()->creatorId();
        $order->save();

        return redirect()->route('sales-orders.show', $order->id)->with('success', __('Order created successfully.'));
    }

    public function customerDetail(Request $request)
    {
        $customer = Client::find($request->id);
        return response()->json($customer);
    }

    public function show($id)
    {
        $order = SalesOrder::where('id', $id)->with(['buying.items', 'po.items', 'pi', 'lc', 'ci.tankers', 'cis.tankers', 'cis.packingList.items', 'cis.consignmentNote.weightSlips', 'cis.delivery', 'customer'])->first();

        $units = \App\Models\ProductServiceUnit::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'name')->toArray();
        $currencies = \App\Models\SalesCurrency::where('created_by', Auth::user()->creatorId())->get()->pluck('code', 'code')->toArray();
        $suppliers = Supplier::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();

        // Add default if empty or ensure Pc/D. are available
        if (empty($units))
            $units = ['MT' => 'MT', 'KG' => 'KG', 'Ltr' => 'Ltr', 'Pc' => 'Pc'];
        else
            $units['Pc'] = 'Pc';

        if (empty($currencies))
            $currencies = ['USD' => 'USD', 'BDT' => 'BDT', 'EUR' => 'EUR', 'D.' => 'D.'];
        else
            $currencies['D.'] = 'D.';

        return view('sales_orders.show', compact('order', 'units', 'currencies', 'suppliers'));
    }

    public function buyingStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        DB::transaction(function () use ($request, $order) {
            $buying = SalesBuyingDetail::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'supplier_id' => $request->supplier_id,
                    'supplier_name' => $request->supplier_name,
                    'total_amount' => $request->total_amount,
                    'created_by' => Auth::user()->creatorId(),
                ]
            );

            $buying->items()->delete();
            foreach ($request->items as $item) {
                SalesBuyingItem::create([
                    'buying_id' => $buying->id,
                    'item_name' => $item['item'],
                    'description' => $item['description'],
                    'quantity' => $item['qty'],
                    'unit' => $item['unit'],
                    'price' => $item['price'],
                    'currency' => $item['currency'] ?? 'D.',
                    'total' => $item['total'],
                ]);
            }

            // Generate Payable Bill (Pending Approval)
            $this->generateSalesPayable($order, $buying, 'Product Purchase');

            $order->current_step = 'PO';
            $order->save();
        });

        return redirect()->back()->with('success', __('Buying details saved and Payable bill generated.'));
    }

    public function poStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        DB::transaction(function () use ($request, $order) {
            $updateData = [
                'po_number' => $request->po_number ?? 'PO-' . time(),
                'client_name' => $request->client_name,
                'client_address' => $request->client_address,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'hs_code' => $request->hs_code,
                'grand_total' => $request->grand_total,
                'signature' => $request->signature,
                'terms_and_conditions' => $request->terms_and_conditions,
                'status' => $request->status ?? 'Pending',
                'prepared_by' => $request->prepared_by,
                'issued_by' => $request->issued_by,
                'acknowledged_by' => $request->acknowledged_by,
                'created_by' => Auth::user()->creatorId(),
            ];

            if ($request->hasFile('file')) {
                $fileName = time() . '_po_' . str_replace(' ', '_', $request->file->getClientOriginalName());
                $request->file->storeAs('uploads/sales_orders', $fileName);
                $updateData['file_path'] = 'uploads/sales_orders/' . $fileName;
            }

            $po = SalesPO::updateOrCreate(
                ['order_id' => $order->id],
                $updateData
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
                    'currency' => $item['currency'] ?? 'D.',
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

        $pi_date = $request->pi_date;
        if ($pi_date) {
            try {
                $pi_date = \Carbon\Carbon::createFromFormat('m-d-Y', $pi_date)->format('Y-m-d');
            } catch (\Exception $e) {
                $pi_date = date('Y-m-d');
            }
        }

        SalesPI::updateOrCreate(
            ['order_id' => $order->id],
            [
                'pi_number' => $request->pi_number,
                'client_pi_number' => $request->client_pi_number,
                'pi_date' => $pi_date,
                'validity' => $request->validity,
                'lifting_time' => $request->lifting_time,
                'payment_terms' => $request->payment_terms,
                'hs_code' => $request->hs_code,
                'country_of_origin' => $request->country_of_origin,
                'tolerance' => $request->tolerance,
                'port_of_loading' => $request->port_of_loading,
                'port_of_discharge' => $request->port_of_discharge,
                'amount' => $request->amount ?? (optional($order->po)->grand_total ?? 0),
                'seller_name' => $request->seller_name,
                'seller_address' => $request->seller_address,
                'seller_mobile' => $request->seller_mobile,
                'seller_email' => $request->seller_email,
                'buyer_name' => $request->buyer_name,
                'buyer_address' => $request->buyer_address,
                'buyer_mobile' => $request->buyer_mobile,
                'buyer_email' => $request->buyer_email,
                'incoterm' => $request->incoterm,
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'branch' => $request->branch,
                'account_no' => $request->account_no,
                'swift_code' => $request->swift_code,
                'terms_and_conditions' => $request->terms_and_conditions,
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
                'lc_reference_no' => $request->lc_reference_no,
                'client_lc_no' => $request->client_lc_no,
                'lc_type' => $request->lc_type,
                'lc_qty' => $request->lc_qty,
                'unit' => $request->unit,
                'lc_date' => $request->lc_date,
                'latest_shipment_date' => $request->latest_shipment_date,
                'lc_validity_date' => $request->lc_validity_date,
                'seller_name' => $request->seller_name,
                'seller_address' => $request->seller_address,
                'seller_mobile' => $request->seller_mobile,
                'seller_email' => $request->seller_email,
                'buyer_name' => $request->buyer_name,
                'buyer_address' => $request->buyer_address,
                'buyer_mobile' => $request->buyer_mobile,
                'buyer_email' => $request->buyer_email,
                'lifting_time' => $request->lifting_time,
                'country_of_origin' => $request->country_of_origin,
                'tolerance' => $request->tolerance,
                'port_of_loading' => $request->port_of_loading,
                'port_of_discharge' => $request->port_of_discharge,
                'terms_and_conditions' => $request->terms_and_conditions,
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

        $totalOrderQty = $order->po && $order->po->items ? $order->po->items->sum('quantity') : 0;
        
        $proposedQty = 0;
        if ($request->has('tankers')) {
            foreach ($request->tankers as $tankerData) {
                $proposedQty += (float)($tankerData['qty_mt'] ?? 0);
            }
        }

        $otherCis = $order->cis;
        if ($request->ci_id) {
            $otherCis = $otherCis->where('id', '!=', $request->ci_id);
        }
        $existingDelivered = $otherCis->flatMap->tankers->sum('quantity_mt');

        if (round($existingDelivered + $proposedQty, 3) > round($totalOrderQty, 3)) {
            return redirect()->back()->with('error', __('Total shipment quantity exceeds Sales Order total quantity. Cannot save shipment.'));
        }

        $transactionResult = DB::transaction(function () use ($request, $order) {
            $ci = SalesCI::updateOrCreate(
                ['id' => $request->ci_id, 'order_id' => $order->id],
                [
                    'pi_id' => $order->pi->id,
                    'lc_id' => $order->lc->id,
                    'ci_number' => $request->ci_number,
                    'client_ci_number' => $request->client_ci_number,
                    'ci_date' => $request->ci_date,
                    'lc_validity_date' => $request->lc_validity_date,
                    'latest_shipment_date' => $request->latest_shipment_date,
                    'created_by' => Auth::user()->creatorId(),
                ]
            );

            $ci->tankers()->delete();
            if ($request->has('tankers')) {
                foreach ($request->tankers as $index => $tankerData) {
                    $filePath = $tankerData['existing_file'] ?? null;

                    if ($request->hasFile("tankers.$index.file")) {
                        $file = $request->file("tankers.$index.file");
                        $fileName = time() . '_tanker_' . $index . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                        $file->storeAs('uploads/sales_orders', $fileName);
                        $filePath = 'uploads/sales_orders/' . $fileName;
                    }

                    SalesCITanker::create([
                        'ci_id' => $ci->id,
                        'tanker_number' => $tankerData['tanker_number'],
                        'quantity_mt' => $tankerData['qty_mt'],
                        'quantity_unit' => $tankerData['quantity_unit'] ?? 'MT',
                        'cpt_usd' => $tankerData['cpt_usd'],
                        'currency' => $tankerData['currency'] ?? 'USD',
                        'total_amount_usd' => $tankerData['total_amount'],
                        'file_path' => $filePath,
                    ]);
                }
            }

            $order->current_step = 'Packing List';
            $order->save();

            // Store CI ID in session for the next steps
            session(['active_ci_id' => $ci->id]);
            return $ci;
        });

        // Notify HRM about possible transport need
        $this->notifyHRMTransportNeed($order, $transactionResult);

        return redirect()->route('sales-orders.show', [$id, 'ci_id' => $transactionResult->id])->with('success', __('CI saved successfully.'))->with('jump_to_pl', true);
    }

    public function plStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        if ($request->hasFile('file')) {
            $fileName = time() . '_' . str_replace(' ', '_', $request->file->getClientOriginalName());
            $request->file->storeAs('uploads/sales_orders', $fileName);
            $filePath = 'uploads/sales_orders/' . $fileName;
        }

        $ci_id = $request->ci_id ?? session('active_ci_id');

        $updateData = [
            'order_id' => $order->id,
            'created_by' => Auth::user()->creatorId(),
        ];
        if (isset($filePath)) {
            $updateData['file_path'] = $filePath;
        }

        SalesPackingList::updateOrCreate(
            ['ci_id' => $ci_id],
            $updateData
        );

        $order->current_step = 'Consignment Note';
        $order->save();

        return redirect()->back()->with('success', __('Packing List saved successfully.'))->with('jump_to_cn', true);
    }

    public function cnStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        DB::transaction(function () use ($request, $order) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_cn_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->storeAs('uploads/sales_orders', $fileName);
                $filePath = 'uploads/sales_orders/' . $fileName;
            }

            $ci_id = $request->ci_id ?? session('active_ci_id');

            $cn = SalesConsignmentNote::firstOrNew(['ci_id' => $ci_id]);
            $cn->order_id = $order->id;
            if (isset($filePath)) {
                $cn->file_path = $filePath;
            }
            $cn->save();

            // Handle per-tanker files if any
            if ($request->hasFile('tanker_files')) {
                $ci = SalesCI::find($ci_id);
                if ($ci) {
                    foreach ($request->file('tanker_files') as $idx => $file) {
                        $tanker = $ci->tankers->get($idx);
                        if ($tanker) {
                            $fileName = time() . '_tanker_cn_' . $idx . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                            $file->storeAs('uploads/sales_orders', $fileName);
                            $tanker->file_path = 'uploads/sales_orders/' . $fileName;
                            $tanker->save();
                        }
                    }
                }
            }

            $cn->weightSlips()->delete();
            if ($request->has('weight_slips')) {
                foreach ($request->weight_slips as $slip) {
                    SalesWeightSlip::create([
                        'consignment_note_id' => $cn->id,
                        'tanker_id' => $slip['tanker_id'] ?? 'N/A', // fallback to N/A if tanker_id is null
                        'gross_weight' => $slip['gross'],
                        'tare_weight' => $slip['tare'],
                        'net_weight' => $slip['net'],
                    ]);
                }
            }

            $order->current_step = 'Received Details';
            $order->save();
        });

        return redirect()->back()->with('success', __('Consignment Note saved successfully.'))->with('jump_to_rd', true);
    }

    public function receivedDetailsStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        $ci_id = $request->ci_id ?? session('active_ci_id');

        if ($request->has('tankers')) {
            $order->tankers_data = $request->tankers;
        }

        $order->status = 'completed'; // Order is partially completed
        $order->save();

        return redirect()->back()->with('success', __('Received details saved.'))->with('jump_to_delivery', true);
    }

    public function deliveryStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        $ci_id = $request->ci_id ?? session('active_ci_id');

        $delivery = \App\Models\SalesDelivery::updateOrCreate(
            ['ci_id' => $ci_id, 'order_id' => $order->id],
            [
                'delivery_mode' => $request->delivery_mode,
                'packing_type' => $request->packing_type,
                'total_quantity_mt' => $request->total_quantity_mt,
                'total_quantity_kg' => $request->total_quantity_kg,
                'required_units' => $request->required_units,
                'drum_qty' => $request->drum_qty ?? 0,
                'drum_unit' => $request->drum_unit,
                'drum_buying_price' => $request->drum_buying_price ?? 0,
                'drum_buying_total' => $request->drum_buying_total ?? 0,
                'drum_selling_price' => $request->drum_selling_price ?? 0,
                'drum_selling_total' => $request->drum_selling_total ?? 0,
                'created_by' => \Auth::user()->creatorId(),
            ]
        );

        // Generate Billing for Drums if any
        if ($delivery->drum_qty > 0) {
            $this->generateSalesPayable($order, $delivery, 'Drum Purchase', $ci_id);
            $this->generateSalesReceivable($order, $delivery, 'Drum Sale', $ci_id);
        }

        // Also generate Product Receivable if first time
        $existingReceivable = \App\Models\Receivable::where('sales_order_id', $order->id)->where('ci_id', null)->first();
        if (!$existingReceivable && $order->po) {
            $this->generateSalesReceivable($order, $order->po, 'Product Sale');
        }

        // Transition order status so HRM/Transport can see it
        $order->status = 'finalized';
        $order->save();

        try {
            $accountsUsers = \App\Models\User::where('type', 'company')->orWhereHas('roles', function($q){
                $q->where('name', 'like', '%Account%');
            })->pluck('id');
            foreach ($accountsUsers as $accUserId) {
                \App\Models\Notification::create([
                    'user_id' => $accUserId,
                    'type' => 'delivery_confirmed',
                    'title' => __('Delivery Confirmed'),
                    'message' => 'Delivery confirmed for Order ' . $order->order_number . '. Please collect the bill from the client.',
                    'related_model' => 'SalesOrder',
                    'related_id' => $order->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Notification not sent (delivery_confirmed): ' . $e->getMessage());
        }

        // Notify Transport
        $this->notifyTransportOrderFinalized($order);

        return redirect()->back()->with('success', __('Delivery Order created and sent to Transport Management.'));
    }

    public function finalize($id)
    {
        $order = SalesOrder::find($id);
        $order->status = 'finalized';
        $order->save();

        // Notify Transport
        $this->notifyTransportOrderFinalized($order);

        return redirect()->back()->with('success', __('Sales Order finalized and sent to Transport Management.'));
    }

    private function notifyTransportOrderFinalized($order)
    {
        $recipientIds = User::where('type', 'company')->pluck('id');
        try {
            // Find users who have permission to manage employees (often logistics roles)
            $recipientIds = $recipientIds->merge(User::permission('Manage Employees')->pluck('id'))->unique()->values();
        } catch (\Throwable $e) {
            \Log::warning('notifyTransportOrderFinalized permission lookup failed: ' . $e->getMessage());
        }

        foreach ($recipientIds as $userId) {
            try {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'sales_order_finalized',
                    'title' => __('New Transport Request'),
                    'message' => __('Order :order has been finalized and is ready for transport.', ['order' => $order->order_number]),
                    'related_model' => 'SalesOrder',
                    'related_id' => $order->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Notification not sent (sales_order_finalized): ' . $e->getMessage());
            }
        }
    }

    private function notifyHRMTransportNeed($order, $ci)
    {
        $recipientIds = User::whereIn('type', ['HR', 'company'])->pluck('id');
        try {
            $recipientIds = $recipientIds->merge(User::permission('show hrm dashboard')->pluck('id'))->unique()->values();
        } catch (\Throwable $e) {
            \Log::warning('notifyHRMTransportNeed permission lookup failed: ' . $e->getMessage());
        }

        foreach ($recipientIds as $userId) {
            try {
                Notification::create([
                    'user_id' => $userId,
                    'type' => 'ci_transport_need',
                    'title' => __('Possible Transport Need'),
                    'message' => __('A new CI has been created for Order :order. Transport may be required.', ['order' => $order->order_number]),
                    'related_model' => 'SalesCI',
                    'related_id' => $ci->id,
                    'created_by' => Auth::user()->id,
                    'is_read' => 0,
                ]);
            } catch (\Throwable $e) {
                \Log::error('Notification not sent (ci_transport_need): ' . $e->getMessage());
            }
        }
    }

    public function destroyCI($id)
    {
        $ci = SalesCI::find($id);
        if ($ci) {
            $ci->delete();
            return redirect()->back()->with('success', __('Shipment batch deleted successfully.'));
        }
        return redirect()->back()->with('error', __('Shipment batch not found.'));
    }

    // Print & Download Methods
    public function poPrint($id)
    {
        $order = SalesOrder::with(['po.items'])->find($id);
        $amountInWords = $this->numberToWords($order->po->grand_total ?? 0);
        return view('sales_orders.print.po', compact('order', 'amountInWords'));
    }
    public function poDownload(Request $request, $id)
    {
        $order = SalesOrder::with(['po.items'])->find($id);
        
        if ($request->isMethod('post') && $order->po) {
            $order->po->update([
                'port_of_loading' => $request->port_of_loading,
                'port_of_discharge' => $request->port_of_discharge,
                'final_destination' => $request->final_destination,
                'country_of_origin' => $request->country_of_origin,
                'packing' => $request->packing,
                'transport_mode' => $request->transport_mode,
                'terms_and_conditions' => $request->general_terms,
            ]);
        }
        
        return view('sales_orders.print.po', compact('order')); // Browser can print to PDF
    }

    public function piPrint($id)
    {
        $order = SalesOrder::with(['pi'])->find($id);
        return view('sales_orders.print.pi', compact('order'));
    }
    public function piDownload($id)
    {
        $order = SalesOrder::with(['pi'])->find($id);
        return view('sales_orders.print.pi', compact('order'));
    }

    public function lcPrint($id)
    {
        $order = SalesOrder::with(['lc'])->find($id);
        return view('sales_orders.print.lc', compact('order'));
    }
    public function lcDownload($id)
    {
        $order = SalesOrder::with(['lc'])->find($id);
        return view('sales_orders.print.lc', compact('order'));
    }

    public function ciPrint($id)
    {
        $order = SalesOrder::with(['ci.tankers'])->find($id);
        return view('sales_orders.print.ci', compact('order'));
    }
    public function ciDownload(\Illuminate\Http\Request $request, $id)
    {
        $order = SalesOrder::find($id);
        if ($request->has('ci_id')) {
            $ci = \App\Models\SalesCI::with('tankers')->find($request->ci_id);
            // Explicitly set the loaded CI onto the order's relation so the view can access it
            $order->setRelation('ci', $ci);
        } else {
            $order->load(['ci.tankers']);
        }
        return view('sales_orders.print.ci', compact('order'));
    }

    public function plPrint($id)
    {
        $order = SalesOrder::with(['packingList'])->find($id);
        return view('sales_orders.print.pl', compact('order'));
    }
    public function plDownload($id)
    {
        $order = SalesOrder::with(['packingList'])->find($id);
        return view('sales_orders.print.pl', compact('order'));
    }

    public function cnPrint($id)
    {
        $order = SalesOrder::with(['consignmentNote.weightSlips.tanker'])->find($id);
        return view('sales_orders.print.cn', compact('order'));
    }

    public function cnDownload($id)
    {
        $order = SalesOrder::with(['consignmentNote.weightSlips.tanker'])->find($id);
        return view('sales_orders.print.cn', compact('order'));
    }

    private function generateSalesPayable($order, $source, $type, $ci_id = null)
    {
        $unique_id = 'PAY-ORD-' . $order->id . '-' . ($ci_id ? $ci_id . '-' : '') . time() . '-' . mt_rand(1000, 9999);
        $payable = Payable::create([
            'unique_id' => $unique_id,
            'invoice_number' => $order->order_number,
            'date' => date('Y-m-d'),
            'billing_direction' => ($type == 'Product Purchase') ? 'supplier' : 'consultant',
            'entity_id' => ($type == 'Product Purchase') ? ($source->supplier_id ?? 0) : 0,
            'sales_order_id' => $order->id,
            'ci_id' => $ci_id,
            'total_amount' => ($type == 'Product Purchase') ? $source->total_amount : $source->drum_buying_total,
            'status' => 'unpaid',
            'approval_status' => 'Pending Approval',
            'created_by' => Auth::user()->creatorId(),
        ]);

        if ($type == 'Product Purchase') {
            foreach ($source->items as $item) {
                PayableItem::create([
                    'payable_id' => $payable->id,
                    'serial' => $item->item_name,
                    'order_details' => $item->description,
                    'qty' => $item->quantity,
                    'rate' => $item->price,
                    'amount' => $item->total,
                ]);
            }
        } else {
            PayableItem::create([
                'payable_id' => $payable->id,
                'serial' => 'Drums',
                'order_details' => 'Drums for delivery ' . ($ci_id ?? ''),
                'qty' => $source->drum_qty,
                'rate' => $source->drum_buying_price,
                'amount' => $source->drum_buying_total,
            ]);
        }

        return $payable;
    }

    private function generateSalesReceivable($order, $source, $type, $ci_id = null)
    {
        $unique_id = 'REC-ORD-' . $order->id . '-' . ($ci_id ? $ci_id . '-' : '') . time() . '-' . mt_rand(1000, 9999);
        $receivable = Receivable::create([
            'unique_id' => $unique_id,
            'invoice_number' => $order->order_number,
            'date' => date('Y-m-d'),
            'billing_direction' => 'client',
            'entity_id' => $order->customer_id,
            'sales_order_id' => $order->id,
            'ci_id' => $ci_id,
            'total_amount' => ($type == 'Product Sale') ? $source->grand_total : $source->drum_selling_total,
            'status' => 'unpaid',
            'approval_status' => 'Pending Approval',
            'created_by' => Auth::user()->creatorId(),
        ]);

        if ($type == 'Product Sale') {
            foreach ($source->items as $item) {
                ReceivableItem::create([
                    'receivable_id' => $receivable->id,
                    'serial' => $item->item_name,
                    'order_details' => $item->description,
                    'qty' => $item->quantity,
                    'rate' => $item->price,
                    'amount' => $item->total,
                ]);
            }
        } else {
            ReceivableItem::create([
                'receivable_id' => $receivable->id,
                'serial' => 'Drums',
                'order_details' => 'Drums for delivery ' . ($ci_id ?? ''),
                'qty' => $source->drum_qty,
                'rate' => $source->drum_selling_price,
                'amount' => $source->drum_selling_total,
            ]);
        }

        return $receivable;
    }

    private function numberToWords($number)
    {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            trigger_error(
                'numberToWords only accepts integers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->numberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[(int) $hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->numberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->numberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $conjunction;
            $string .= $fraction . '/100';
        }

        return ucfirst($string);
    }
    public function destroy($id)
    {
        $order = SalesOrder::find($id);
        if ($order) {
            $order->delete();
            return redirect()->route('sales-orders.index')->with('success', __('Sales Order deleted successfully.'));
        }
        return redirect()->route('sales-orders.index')->with('error', __('Sales Order not found.'));
    }
}
