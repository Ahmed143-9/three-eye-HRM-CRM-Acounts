<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Utility;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        return view('accounting_setup.suppliers.index', compact('suppliers'));
    }

    public function getSuppliers()
    {
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        return response()->json($suppliers);
    }

    public function create()
    {
        $lastId = Supplier::orderBy('id', 'desc')->first();
        $nextId = $lastId ? $lastId->id + 1 : 1;
        $unique_id = Utility::supplierNumberFormat($nextId);
        return view('accounting_setup.suppliers.create', compact('unique_id'));
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'unique_id' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tin_no' => 'nullable|string|max:100',
            'bin_number' => 'nullable|string|max:100',
            'irc_no' => 'nullable|string|max:100',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_number' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'head_office_address' => 'nullable|string',
            'factory_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'file_attachment.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        try {
            $data = $request->except('_token', 'file_attachment');
            
            if ($request->hasFile('file_attachment')) {
                $files = [];
                foreach ($request->file('file_attachment') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('uploads/suppliers', $fileName, 'public');
                    $files[] = 'uploads/suppliers/' . $fileName;
                }
                $data['file_attachment'] = json_encode($files);
            }

            $data['created_by'] = auth()->id() ?? 0;
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            Supplier::create($data);

            return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('accounting_setup.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'unique_id' => 'required|string|max:100|unique:suppliers,unique_id,' . $supplier->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tin_no' => 'nullable|string|max:100',
            'bin_number' => 'nullable|string|max:100',
            'irc_no' => 'nullable|string|max:100',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_number' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'head_office_address' => 'nullable|string',
            'factory_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'file_attachment.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('_token', 'file_attachment', '_method');

        if ($request->hasFile('file_attachment')) {
            $files = $supplier->file_attachment ? json_decode($supplier->file_attachment, true) : [];
            if (!is_array($files)) {
                $files = [];
            }
            foreach ($request->file('file_attachment') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('uploads/suppliers', $fileName, 'public');
                $files[] = 'uploads/suppliers/' . $fileName;
            }
            $data['file_attachment'] = json_encode($files);
        }

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $supplier->update($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
