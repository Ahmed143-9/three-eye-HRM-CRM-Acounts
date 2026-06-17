<?php

namespace App\Http\Controllers;

use App\Models\Incoterm;
use Illuminate\Http\Request;

class IncotermController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Accounting Setup') || \Auth::user()->can('Manage HR Setup')) {
            $incoterms = Incoterm::where('created_by', '=', \Auth::user()->creatorId())->get();
            return view('incoterm.index', compact('incoterms'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Manage Accounting Setup') || \Auth::user()->can('Manage HR Setup')) {
            return view('incoterm.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Manage Accounting Setup') || \Auth::user()->can('Manage HR Setup')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:200',
                ]
            );
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $incoterm             = new Incoterm();
            $incoterm->name       = $request->name;
            $incoterm->created_by = \Auth::user()->creatorId();
            $incoterm->save();

            return redirect()->route('incoterm.index')->with('success', __('Incoterm successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(Incoterm $incoterm)
    {
        if (\Auth::user()->can('Manage Accounting Setup') || \Auth::user()->can('Manage HR Setup')) {
            if ($incoterm->created_by == \Auth::user()->creatorId()) {
                return view('incoterm.edit', compact('incoterm'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Incoterm $incoterm)
    {
        if (\Auth::user()->can('Manage Accounting Setup') || \Auth::user()->can('Manage HR Setup')) {
            if ($incoterm->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:200',
                    ]
                );
                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $incoterm->name = $request->name;
                $incoterm->save();

                return redirect()->route('incoterm.index')->with('success', __('Incoterm successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Incoterm $incoterm)
    {
        if (\Auth::user()->can('Manage Accounting Setup') || \Auth::user()->can('Manage HR Setup')) {
            if ($incoterm->created_by == \Auth::user()->creatorId()) {
                $incoterm->delete();
                return redirect()->route('incoterm.index')->with('success', __('Incoterm successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
