<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryBatch;
use App\Models\InventoryUsage;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->type == 'Employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = Auth::user()->creatorId();
        $items = InventoryItem::where('created_by', $creatorId)
            ->with(['batches'])
            ->get();

        foreach ($items as $item) {
            $item->total_purchased = $item->batches->sum('quantity_purchased');
            $item->total_available = $item->batches->sum('quantity_available');
            $item->total_used = $item->total_purchased - $item->total_available;
            
            $totalVal = $item->batches->sum(function ($b) {
                return $b->quantity_available * $b->unit_cost;
            });
            $item->total_value = $totalVal;
            $item->weighted_average_cost = $item->total_available > 0 ? ($totalVal / $item->total_available) : 0;
        }

        return view('inventory.index', compact('items'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->type == 'Employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
        ]);

        InventoryItem::create([
            'name' => $request->name,
            'type' => $request->type ?? 'Consumable',
            'unit' => $request->unit,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('inventory.index')->with('success', __('Inventory Item created successfully.'));
    }

    public function show($id)
    {
        if (Auth::user()->type == 'Employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = Auth::user()->creatorId();
        $item = InventoryItem::where('id', $id)->where('created_by', $creatorId)->firstOrFail();
        
        $batches = InventoryBatch::where('inventory_item_id', $item->id)
            ->with('supplier')
            ->orderBy('purchase_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $usages = InventoryUsage::where('inventory_item_id', $item->id)
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get list of suppliers for adding purchase batches
        $suppliers = Supplier::where('created_by', $creatorId)->get()->pluck('name', 'id')->toArray();

        return view('inventory.show', compact('item', 'batches', 'usages', 'suppliers'));
    }

    public function storeBatch(Request $request, $id)
    {
        if (Auth::user()->type == 'Employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $creatorId = Auth::user()->creatorId();
        $item = InventoryItem::where('id', $id)->where('created_by', $creatorId)->firstOrFail();

        $request->validate([
            'purchase_date' => 'required|date',
            'quantity_purchased' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $qty = $request->quantity_purchased;
        $cost = $request->unit_cost;

        $batch = InventoryBatch::create([
            'inventory_item_id' => $item->id,
            'supplier_id' => $request->supplier_id,
            'purchase_date' => $request->purchase_date,
            'quantity_purchased' => $qty,
            'quantity_available' => $qty,
            'unit_cost' => $cost,
            'total_cost' => $qty * $cost,
            'created_by' => $creatorId,
        ]);

        // Generate Accounts Payable Bill
        $latest = \App\Models\Bill::where('created_by', '=', $creatorId)->latest('bill_id')->first();
        $billNumber = $latest ? $latest->bill_id + 1 : 1;
        $category = \App\Models\ProductServiceCategory::where('created_by', $creatorId)->where('type', 'expense')->first();

        $bill = new \App\Models\Bill();
        $bill->bill_id = $billNumber;
        $bill->vender_id = $request->supplier_id;
        $bill->bill_date = $request->purchase_date;
        $bill->due_date = $request->purchase_date;
        $bill->category_id = $category ? $category->id : 0;
        $bill->order_number = 0;
        $bill->status = 0;
        $bill->created_by = $creatorId;
        $bill->save();

        \App\Models\BillProduct::create([
            'bill_id' => $bill->id,
            'product_id' => 0,
            'quantity' => $qty,
            'tax' => 0,
            'discount' => 0,
            'price' => $cost,
            'description' => "Inventory Purchase: " . $item->name,
        ]);

        return redirect()->route('inventory.show', $item->id)->with('success', __('Purchase batch and Payable Bill created successfully.'));
    }

    public function getItemCost($id)
    {
        $creatorId = Auth::user()->creatorId();
        $item = InventoryItem::where('id', $id)->where('created_by', $creatorId)->first();
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $batches = InventoryBatch::where('inventory_item_id', $item->id)
            ->where('quantity_available', '>', 0)
            ->get();

        $totalQty = $batches->sum('quantity_available');
        $totalVal = $batches->sum(function ($b) {
            return $b->quantity_available * $b->unit_cost;
        });

        $avgCost = $totalQty > 0 ? ($totalVal / $totalQty) : 0;

        return response()->json([
            'available_qty' => $totalQty,
            'average_cost' => round($avgCost, 2),
            'unit' => $item->unit,
        ]);
    }
}
