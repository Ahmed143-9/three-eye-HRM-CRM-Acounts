<?php

namespace App\Http\Controllers;

use App\Models\PettyCashAllocation;
use App\Models\PettyCashUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PettyCashController extends Controller
{
    public function index(Request $request)
    {
        $allocations = PettyCashAllocation::with('admin')->orderBy('month', 'desc')->get();
        return view('petty_cash.index', compact('allocations'));
    }

    public function show($id)
    {
        $allocation = PettyCashAllocation::with(['usages.user', 'admin'])->findOrFail($id);
        return view('petty_cash.show', compact('allocation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|string', // e.g. 2026-05
            'allocated_amount' => 'required|numeric|min:0',
        ]);

        // Check if allocation already exists
        $exists = PettyCashAllocation::where('month', $request->month)->first();
        if ($exists) {
            return redirect()->back()->with('error', __('Allocation for this month already exists.'));
        }

        // Calculate rollover from previous month
        $previousMonth = date('Y-m', strtotime($request->month . '-01 -1 month'));
        $previousAllocation = PettyCashAllocation::where('month', $previousMonth)->first();
        
        $rollover = 0;
        if ($previousAllocation) {
            $rollover = $previousAllocation->total_amount - $previousAllocation->used_amount;
            if ($rollover < 0) $rollover = 0;
        }

        $allocation = new PettyCashAllocation();
        $allocation->month = $request->month;
        $allocation->allocated_amount = $request->allocated_amount;
        $allocation->rollover_amount = $rollover;
        $allocation->total_amount = $request->allocated_amount + $rollover;
        $allocation->used_amount = 0;
        $allocation->allocated_by = Auth::user()->id;
        $allocation->save();

        return redirect()->route('petty-cash.index')->with('success', __('Petty cash allocated successfully.'));
    }

    public function storeUsage(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string'
        ]);

        $allocation = PettyCashAllocation::findOrFail($id);

        $usage = new PettyCashUsage();
        $usage->petty_cash_allocation_id = $allocation->id;
        $usage->date = $request->date;
        $usage->amount = $request->amount;
        $usage->purpose = $request->purpose;
        $usage->user_id = Auth::user()->id;
        $usage->save();

        // Update total used
        $allocation->used_amount += $request->amount;
        $allocation->save();

        return redirect()->route('petty-cash.show', $allocation->id)->with('success', __('Usage recorded successfully.'));
    }

    public function downloadPdf($id)
    {
        $allocation = PettyCashAllocation::with(['usages.user', 'admin'])->findOrFail($id);
        
        $pdf = Pdf::loadView('petty_cash.pdf', compact('allocation'));
        return $pdf->download('PettyCash_Report_' . $allocation->month . '.pdf');
    }
}
