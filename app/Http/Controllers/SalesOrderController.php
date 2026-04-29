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
use Carbon\Carbon;

class SalesOrderController extends Controller
{
    public function index()
    {
        $orders = SalesOrder::where('created_by', Auth::user()->creatorId())->with('customer')->get();
        return view('sales_orders.index', compact('orders'));
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
        $order->current_step = 'PO';
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
        $order = SalesOrder::find($id);
        if (!$order) return redirect()->route('sales-orders.index')->with('error', __('Order not found.'));
        
        $step = strtolower($order->current_step);
        if ($step == 'packing list') $step = 'pl';
        if ($step == 'consignment note') $step = 'cn';
        if ($step == 'received details') $step = 'rd';
        
        return redirect()->route('sales-orders.' . $step, $id);
    }

    private function getCommonData() {
        $units = \App\Models\ProductServiceUnit::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'name')->toArray();
        $currencies = \App\Models\SalesCurrency::where('created_by', Auth::user()->creatorId())->get()->pluck('code', 'code')->toArray();
        if(empty($units)) $units = ['MT' => 'MT', 'KG' => 'KG', 'Ton' => 'Ton', 'Ltr' => 'Ltr', 'Pcs' => 'Pcs'];
        if(empty($currencies)) $currencies = ['USD' => 'USD', 'BDT' => 'BDT', 'INR' => 'Rupee (INR)', 'EUR' => 'EUR', 'GBP' => 'GBP'];
        return compact('units', 'currencies');
    }

    public function poPage($id) {
        $order = SalesOrder::where('id', $id)->with(['po.items', 'customer'])->first();
        return view('sales_orders.workflow.po', array_merge(['order' => $order], $this->getCommonData()));
    }

    public function piPage($id) {
        $order = SalesOrder::where('id', $id)->with(['po.items', 'pi', 'customer'])->first();
        if (!$order->po) return redirect()->route('sales-orders.po', $id)->with('error', __('Please complete PO first.'));
        return view('sales_orders.workflow.pi', array_merge(['order' => $order], $this->getCommonData()));
    }

    public function lcPage($id) {
        $order = SalesOrder::where('id', $id)->with(['po.items', 'pi', 'lc', 'customer'])->first();
        if (!$order->pi) return redirect()->route('sales-orders.pi', $id)->with('error', __('Please complete PI first.'));
        return view('sales_orders.workflow.lc', array_merge(['order' => $order], $this->getCommonData()));
    }

    public function ciPage($id) {
        $order = SalesOrder::where('id', $id)->with(['po.items', 'pi', 'lc', 'ci.tankers', 'customer'])->first();
        if (!$order->lc) return redirect()->route('sales-orders.lc', $id)->with('error', __('Please complete LC first.'));
        return view('sales_orders.workflow.ci', array_merge(['order' => $order], $this->getCommonData()));
    }

    public function plPage($id) {
        $order = SalesOrder::where('id', $id)->with(['ci.tankers', 'packingList.items', 'customer'])->first();
        if (!$order->ci) return redirect()->route('sales-orders.ci', $id)->with('error', __('Please complete CI first.'));
        return view('sales_orders.workflow.pl', array_merge(['order' => $order], $this->getCommonData()));
    }

    public function cnPage($id) {
        $order = SalesOrder::where('id', $id)->with(['ci.tankers', 'packingList', 'consignmentNote.weightSlips', 'customer'])->first();
        if (!$order->packingList) return redirect()->route('sales-orders.pl', $id)->with('error', __('Please complete Packing List first.'));
        return view('sales_orders.workflow.cn', array_merge(['order' => $order], $this->getCommonData()));
    }

    public function receivedDetailsPage($id) {
        $order = SalesOrder::where('id', $id)->with(['consignmentNote.weightSlips', 'customer'])->first();
        if (!$order->consignmentNote) return redirect()->route('sales-orders.cn', $id)->with('error', __('Please complete Consignment Note first.'));
        return view('sales_orders.workflow.received_details', array_merge(['order' => $order], $this->getCommonData()));
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
                    'hs_code' => $request->hs_code,
                    'grand_total' => $request->grand_total,
                    'signature' => $request->signature,
                    'terms_and_conditions' => $request->terms_and_conditions,
                    'created_by' => Auth::user()->creatorId(),
                ]
            );

            $po->items()->delete();
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    SalesPOItem::create([
                        'po_id' => $po->id,
                        'item_name' => $item['item'],
                        'description' => $item['description'],
                        'quantity' => $item['qty'],
                        'unit_id' => $item['unit'],
                        'price_per_unit' => $item['price'],
                        'currency_type' => $item['currency'] ?? 'BDT',
                        'total' => $item['total'],
                    ]);
                }
            }

            $order->current_step = 'PI';
            $order->save();
        });

        return redirect()->route('sales-orders.pi', $order->id)->with('success', __('PO saved successfully.'));
    }

    public function piStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        SalesPI::updateOrCreate(
            ['order_id' => $order->id],
            [
                'pi_number' => $request->pi_number,
                'client_pi_number' => $request->client_pi_number,
                'pi_date' => $this->parseDate($request->pi_date),
                'validity' => $request->validity,
                'lifting_time' => $request->lifting_time,
                'payment_terms' => $request->payment_terms,
                'hs_code' => $request->hs_code,
                'country_of_origin' => $request->country_of_origin,
                'tolerance' => $request->tolerance,
                'port_of_loading' => $request->port_of_loading,
                'port_of_discharge' => $request->port_of_discharge,
                'seller_name' => $request->seller_name,
                'seller_address' => $request->seller_address,
                'seller_mobile' => $request->seller_mobile,
                'seller_email' => $request->seller_email,
                'buyer_name' => $request->buyer_name,
                'buyer_address' => $request->buyer_address,
                'buyer_mobile' => $request->buyer_mobile,
                'buyer_email' => $request->buyer_email,
                'bank_name' => $request->bank_name,
                'account_name' => $request->account_name,
                'branch_name' => $request->branch_name,
                'account_no' => $request->account_no,
                'swift_code' => $request->swift_code,
                'incoterm' => $request->incoterm,
                'terms_and_conditions' => $request->terms_and_conditions,
                'created_by' => Auth::user()->creatorId(),
            ]
        );

        $order->current_step = 'LC';
        $order->save();

        return redirect()->route('sales-orders.lc', $order->id)->with('success', __('PI saved successfully.'));
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
                'lc_qty' => $request->lc_qty,
                'unit' => $request->unit,
                'date_of_issue' => $this->parseDate($request->date_of_issue),
                'latest_shipment_date' => $this->parseDate($request->latest_shipment_date),
                'lc_validity_date' => $this->parseDate($request->lc_validity_date),
                'lc_type' => $request->lc_type,
                'incoterm' => $request->incoterm,
                'terms_and_conditions' => $request->terms_and_conditions,
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
                'created_by' => Auth::user()->creatorId(),
            ]
        );

        $order->current_step = 'CI';
        $order->save();

        return redirect()->route('sales-orders.ci', $order->id)->with('success', __('LC saved successfully.'));
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
                    'ci_date' => $this->parseDate($request->ci_date),
                    'lc_validity_date' => $this->parseDate($request->lc_validity_date),
                    'latest_shipment_date' => $this->parseDate($request->latest_shipment_date),
                    'created_by' => Auth::user()->creatorId(),
                ]
            );

            $ci->tankers()->delete();
            foreach ($request->tankers as $tanker) {
                SalesCITanker::create([
                    'ci_id' => $ci->id,
                    'tanker_number' => $tanker['tanker_number'],
                    'quantity_mt' => $tanker['qty_mt'],
                    'quantity_unit' => $tanker['quantity_unit'] ?? 'MT',
                    'cpt_usd' => $tanker['cpt_usd'],
                    'currency' => $tanker['currency'] ?? 'USD',
                    'total_amount_usd' => $tanker['total_amount'],
                ]);
            }

            $order->current_step = 'Packing List';
            $order->save();
        });

        return redirect()->route('sales-orders.pl', $order->id)->with('success', __('CI saved successfully.'));
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

        return redirect()->route('sales-orders.cn', $order->id)->with('success', __('Packing List saved successfully.'));
    }

    public function cnStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        DB::transaction(function () use ($request, $order) {
            $cn = SalesConsignmentNote::updateOrCreate(
                ['order_id' => $order->id],
                ['file_path' => null] // We are moving files to individual slips
            );

            // Get existing slips to preserve file paths if no new file is uploaded
            $existingSlips = $cn->weightSlips->pluck('file_path', 'tanker_id')->toArray();

            $cn->weightSlips()->delete();
            if ($request->has('weight_slips')) {
                foreach ($request->weight_slips as $index => $slip) {
                    $filePath = $existingSlips[$slip['tanker_id']] ?? null;

                    if ($request->hasFile("tanker_files.$index")) {
                        $file = $request->file("tanker_files.$index");
                        $fileName = time() . "_cn_{$index}_" . $file->getClientOriginalName();
                        $file->storeAs('public/sales_orders', $fileName);
                        $filePath = 'storage/sales_orders/' . $fileName;
                    }

                    SalesWeightSlip::create([
                        'consignment_note_id' => $cn->id,
                        'tanker_id' => $slip['tanker_id'],
                        'gross_weight' => $slip['gross'],
                        'tare_weight' => $slip['tare'],
                        'net_weight' => $slip['net'],
                        'file_path' => $filePath,
                    ]);
                }
            }

            $order->current_step = 'Received Details';
            $order->save();
        });

        return redirect()->route('sales-orders.rd', $order->id)->with('success', __('Consignment Note saved successfully.'));
    }

    public function receivedDetailsStore(Request $request, $id)
    {
        $order = SalesOrder::find($id);
        $order->tankers_data = $request->tankers;
        $order->status = 'completed';
        $order->save();

        return redirect()->route('sales-orders.rd', $order->id)->with('success', __('Received Details saved successfully.'));
    }

    public function finalize($id)
    {
        $order = SalesOrder::find($id);
        $order->status = 'finalized';
        $order->save();

        return redirect()->back()->with('success', __('Sales Order finalized and sent to Transport Management.'));
    }

    // Print & Download Methods
    public function poPrint($id) {
        $order = SalesOrder::with(['po.items'])->find($id);
        $amountInWords = $this->numberToWords($order->po->grand_total ?? 0);
        return view('sales_orders.print.po', compact('order', 'amountInWords'));
    }
    public function poDownload($id) {
        $order = SalesOrder::with(['po.items'])->find($id);
        return view('sales_orders.print.po', compact('order')); // Browser can print to PDF
    }

    public function piPrint($id) {
        $order = SalesOrder::with(['pi'])->find($id);
        return view('sales_orders.print.pi', compact('order'));
    }
    public function piDownload($id) {
        $order = SalesOrder::with(['pi'])->find($id);
        return view('sales_orders.print.pi', compact('order'));
    }

    public function lcPrint($id) {
        $order = SalesOrder::with(['lc'])->find($id);
        return view('sales_orders.print.lc', compact('order'));
    }
    public function lcDownload($id) {
        $order = SalesOrder::with(['lc'])->find($id);
        return view('sales_orders.print.lc', compact('order'));
    }

    public function ciPrint($id) {
        $order = SalesOrder::with(['ci.tankers'])->find($id);
        return view('sales_orders.print.ci', compact('order'));
    }
    public function ciDownload($id) {
        $order = SalesOrder::with(['ci.tankers'])->find($id);
        return view('sales_orders.print.ci', compact('order'));
    }

    public function plPrint($id) {
        $order = SalesOrder::with(['packingList'])->find($id);
        return view('sales_orders.print.pl', compact('order'));
    }
    public function plDownload($id) {
        $order = SalesOrder::with(['packingList'])->find($id);
        return view('sales_orders.print.pl', compact('order'));
    }

    public function cnPrint($id) {
        $order = SalesOrder::with(['consignmentNote.weightSlips.tanker'])->find($id);
        return view('sales_orders.print.cn', compact('order'));
    }

    public function cnDownload($id) {
        $order = SalesOrder::with(['consignmentNote.weightSlips.tanker'])->find($id);
        return view('sales_orders.print.cn', compact('order'));
    }

    private function numberToWords($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'forty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
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
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
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

    private function parseDate($date)
    {
        if (!$date) return null;
        try {
            return \Carbon\Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return $date; // Fallback to original if already in Y-m-d or other format
        }
    }
}
