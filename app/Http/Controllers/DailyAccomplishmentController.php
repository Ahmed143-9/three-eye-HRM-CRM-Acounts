<?php

namespace App\Http\Controllers;

use App\Models\DailyAccomplishment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyAccomplishmentController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage daily accomplishment') || Auth::user()->type == 'Employee') {
            if (Auth::user()->type == 'Employee') {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $accomplishments = DailyAccomplishment::where('employee_id', $employee->id)->get();
            } else {
                $accomplishments = DailyAccomplishment::where('created_by', Auth::user()->creatorId())->with('employee')->get();
            }

            return view('daily_accomplishments.index', compact('accomplishments'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('create daily accomplishment') || Auth::user()->type == 'Employee') {
            $employees = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            return view('daily_accomplishments.create', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create daily accomplishment') || Auth::user()->type == 'Employee') {
            $validator = \Validator::make(
                $request->all(), [
                    'date' => 'required',
                    'summary' => 'required',
                    'hours_spent' => 'required|numeric',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $accomplishment = new DailyAccomplishment();
            if (Auth::user()->type == 'Employee') {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $accomplishment->employee_id = $employee->id;
            } else {
                $accomplishment->employee_id = $request->employee_id;
            }

            $accomplishment->date = $request->date;
            $accomplishment->summary = $request->summary;
            $accomplishment->challenges = $request->challenges;
            $accomplishment->hours_spent = $request->hours_spent;
            $accomplishment->created_by = Auth::user()->creatorId();
            $accomplishment->save();

            return redirect()->route('daily-accomplishments.index')->with('success', __('Daily Accomplishment successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(DailyAccomplishment $dailyAccomplishment)
    {
        //
    }

    public function edit(DailyAccomplishment $dailyAccomplishment)
    {
        if (Auth::user()->can('edit daily accomplishment') || Auth::user()->type == 'Employee') {
            if (Auth::user()->type == 'Employee' && $dailyAccomplishment->employee_id != Employee::where('user_id', Auth::user()->id)->first()->id) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }

            $employees = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            return view('daily_accomplishments.edit', compact('dailyAccomplishment', 'employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, DailyAccomplishment $dailyAccomplishment)
    {
        if (Auth::user()->can('edit daily accomplishment') || Auth::user()->type == 'Employee') {
            if (Auth::user()->type == 'Employee' && $dailyAccomplishment->employee_id != Employee::where('user_id', Auth::user()->id)->first()->id) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }

            $validator = \Validator::make(
                $request->all(), [
                    'date' => 'required',
                    'summary' => 'required',
                    'hours_spent' => 'required|numeric',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            if (Auth::user()->type != 'Employee') {
                $dailyAccomplishment->employee_id = $request->employee_id;
            }

            $dailyAccomplishment->date = $request->date;
            $dailyAccomplishment->summary = $request->summary;
            $dailyAccomplishment->challenges = $request->challenges;
            $dailyAccomplishment->hours_spent = $request->hours_spent;
            $dailyAccomplishment->save();

            return redirect()->route('daily-accomplishments.index')->with('success', __('Daily Accomplishment successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(DailyAccomplishment $dailyAccomplishment)
    {
        if (Auth::user()->can('delete daily accomplishment') || Auth::user()->type == 'Employee') {
            if (Auth::user()->type == 'Employee' && $dailyAccomplishment->employee_id != Employee::where('user_id', Auth::user()->id)->first()->id) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }

            $dailyAccomplishment->delete();
            return redirect()->route('daily-accomplishments.index')->with('success', __('Daily Accomplishment successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
