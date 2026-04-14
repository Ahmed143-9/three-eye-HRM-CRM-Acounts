<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    public function loanCreate($id)
    {
        $employee = Employee::find($id);
        $loan_options      = LoanOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $loan =loan::$Loantypes;

        return view('loan.create', compact('employee','loan_options','loan'));
    }

    public function store(Request $request)
    {

        if(\Auth::user()->can('create loan'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'employee_id' => 'required',
                                   'loan_option' => 'required',
                                   'title' => 'required',
                                   'amount' => 'required',
                                   'reason' => 'required',
                                   'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $loan              = new Loan();
            $loan->employee_id = $request->employee_id;
            $loan->loan_option = $request->loan_option;
            $loan->title       = $request->title;
            $loan->amount      = $request->amount;
            $loan->type        = $request->type;
            $loan->reason      = $request->reason;
            $loan->created_by  = \Auth::user()->creatorId();

            if ($request->hasFile('attachment')) {
                $file          = $request->file('attachment');
                $originalName  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $originalName  = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
                $originalName  = trim($originalName, '_');
                $fileName      = $originalName . '_loan_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $dir           = 'uploads/loan_attachments/';
                if (!Storage::exists($dir)) {
                    Storage::makeDirectory($dir);
                }
                $file->storeAs($dir, $fileName);
                $loan->attachment = $dir . $fileName;
            }

            $loan->save();

            return redirect()->back()->with('success', __('Loan  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Loan $loan)
    {
        return redirect()->route('commision.index');
    }

    public function edit($loan)
    {
        $loan = Loan::find($loan);
        if(\Auth::user()->can('edit loan'))
        {
            if($loan->created_by == \Auth::user()->creatorId())
            {
                $loan_options = LoanOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $loans =loan::$Loantypes;
                return view('loan.edit', compact('loan', 'loan_options','loans'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Loan $loan)
    {
        if(\Auth::user()->can('edit loan'))
        {
            if($loan->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [

                                       'loan_option' => 'required',
                                       'title' => 'required',
                                       'amount' => 'required',
                                       'reason' => 'required',
                                       'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $loan->loan_option = $request->loan_option;
                $loan->title       = $request->title;
                $loan->type        = $request->type;
                $loan->amount      = $request->amount;
                $loan->reason      = $request->reason;

                if ($request->hasFile('attachment')) {
                    // Delete old attachment if exists
                    if (!empty($loan->attachment) && Storage::exists($loan->attachment)) {
                        Storage::delete($loan->attachment);
                    }

                    $file          = $request->file('attachment');
                    $originalName  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $originalName  = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
                    $originalName  = trim($originalName, '_');
                    $fileName      = $originalName . '_loan_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $dir           = 'uploads/loan_attachments/';
                    if (!Storage::exists($dir)) {
                        Storage::makeDirectory($dir);
                    }
                    $file->storeAs($dir, $fileName);
                    $loan->attachment = $dir . $fileName;
                }

                $loan->save();

                return redirect()->back()->with('success', __('Loan successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Loan $loan)
    {
        if(\Auth::user()->can('delete loan'))
        {
            if($loan->created_by == \Auth::user()->creatorId())
            {
                // Delete attachment file if exists
                if (!empty($loan->attachment) && Storage::exists($loan->attachment)) {
                    Storage::delete($loan->attachment);
                }

                $loan->delete();

                return redirect()->back()->with('success', __('Loan successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function downloadAttachment(Loan $loan)
    {
        if ($loan->created_by != \Auth::user()->creatorId() && \Auth::user()->type !== 'Employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if (empty($loan->attachment) || !Storage::exists($loan->attachment)) {
            return redirect()->back()->with('error', __('Attachment not found.'));
        }

        return Storage::download($loan->attachment, basename($loan->attachment));
    }
}
