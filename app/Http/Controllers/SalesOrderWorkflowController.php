<?php

namespace App\Http\Controllers;

use App\Models\ProductServiceUnit;
use App\Models\SalesCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesOrderWorkflowController extends Controller
{
    public function addUnit(Request $request)
    {
        $unit = ProductServiceUnit::create([
            'name' => $request->name,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $unit,
        ]);
    }

    public function addCurrency(Request $request)
    {
        $currency = SalesCurrency::create([
            'name' => $request->code,
            'code' => $request->code,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $currency,
        ]);
    }
}
