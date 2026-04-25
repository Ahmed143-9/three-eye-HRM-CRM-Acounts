<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use App\Models\Utility;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    public function index()
    {
        $consultants = Consultant::orderBy('id', 'desc')->get();
        return view('accounting_setup.consultants.index', compact('consultants'));
    }

    public function getConsultants()
    {
        $consultants = Consultant::select('id', 'name', 'billing_address')->get();
        return response()->json($consultants);
    }

    public function create()
    {
        $lastId = Consultant::orderBy('id', 'desc')->first();
        $nextId = $lastId ? $lastId->id + 1 : 1;
        $unique_id = Utility::consultantNumberFormat($nextId);
        return view('accounting_setup.consultants.create', compact('unique_id'));
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
                $path = $file->storeAs('uploads/consultants', $fileName, 'public');
                $data['file_attachment'] = $path;
            }

            $data['created_by'] = auth()->id() ?? 0;
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            Consultant::create($data);

            return redirect()->route('consultants.index')->with('success', 'Consultant created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $consultant = Consultant::findOrFail($id);
        return view('accounting_setup.consultants.edit', compact('consultant'));
    }

    public function update(Request $request, $id)
    {
        $consultant = Consultant::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'unique_id' => 'required|string|max:100|unique:consultants,unique_id,' . $consultant->id,
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
            'bank_details' => 'nullable|string',
            'file_attachment' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('_token', 'file_attachment', '_method');

        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/consultants', $fileName, 'public');
            $data['file_attachment'] = $path;
        }

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $consultant->update($data);

        return redirect()->route('consultants.index')->with('success', 'Consultant updated successfully.');
    }

    public function destroy($id)
    {
        $consultant = Consultant::findOrFail($id);
        $consultant->delete();
        
        return redirect()->route('consultants.index')->with('success', 'Consultant deleted successfully.');
    }
}
