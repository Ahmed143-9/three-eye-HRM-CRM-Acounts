<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccountingClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('id', 'desc')->get();
        
        return view('accounting_setup.clients.index', compact('clients'));
    }

    public function getClientInfo($id)
    {
        $client = Client::find($id);
        return response()->json($client);
    }

    public function create()
    {
        $lastId = Client::orderBy('id', 'desc')->first();
        $nextId = $lastId ? $lastId->id + 1 : 1;
        $unique_id = Utility::clientNumberFormat($nextId);
        return view('accounting_setup.clients.create', compact('unique_id'));
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
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'account_no' => 'nullable|string|max:255',
            'bank_details' => 'nullable|string',
            'file_attachment' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        try {
            $data = $request->except('_token', 'file_attachment');
            
            if ($request->hasFile('file_attachment')) {
                $file = $request->file('file_attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads/clients', $fileName, 'public');
                $data['file_attachment'] = $path;
            }

            $data['created_by'] = auth()->id() ?? 0;
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            Client::create($data);

            return redirect()->route('accounting-clients.index')->with('success', 'Client created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('accounting_setup.clients.edit', compact('client'));
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'unique_id' => 'required|string|max:100|unique:clients,unique_id,' . $client->id,
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
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'account_no' => 'nullable|string|max:255',
            'bank_details' => 'nullable|string',
            'file_attachment' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('_token', 'file_attachment', '_method');

        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/clients', $fileName, 'public');
            $data['file_attachment'] = $path;
        }

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $client->update($data);

        return redirect()->route('accounting-clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        
        return redirect()->route('accounting-clients.index')->with('success', 'Client deleted successfully.');
    }
}
