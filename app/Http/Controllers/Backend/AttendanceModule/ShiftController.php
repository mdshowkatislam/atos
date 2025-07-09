<?php

namespace App\Http\Controllers\Backend\AttendanceModule;

use App\Http\Controllers\Controller;
use App\Models\ShiftSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule; 
class ShiftController extends Controller
{
    public function index()
    {
        $shift = ShiftSetting::where('status', 1)->get();

        if ($shift) {
            return view('backend.attendance_module.shift_management.index', compact('shift'));
        }

        return view('backend.attendance_module.shift_management.index');
    }

    public function store(Request $request)
{
    $request->validate([
        'shift_name' => ['required', 'unique:shift_settings,shift_name'],
        'start_time' => ['required', 'date_format:H:i'],
        'end_time' => ['required', 'date_format:H:i'],
    ]);

    $start = Carbon::createFromFormat('H:i', $request->start_time);
    $end = Carbon::createFromFormat('H:i', $request->end_time);

    // Allow overnight shifts (e.g. 22:00 → 06:00 next day)
    if ($start->eq($end)) {
        return back()->withErrors([
            'start_time' => 'Start time and end time cannot be the same.',
            'end_time' => 'Start time and end time cannot be the same.',
        ])->withInput();
    }

    // Laravel considers 06:00 < 22:00 if same-day, so adjust
    if ($end->lt($start)) {
        $end->addDay(); // treat end as next day
    }

    if ($end->lessThanOrEqualTo($start)) {
        return back()->withErrors([
            'start_time' => 'Start time must be before end time.',
            'end_time' => 'End time must be after start time.',
        ])->withInput();
    }

    // Continue saving data
    ShiftSetting::create([
        'shift_name' => $request->shift_name,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'description' => $request->description,
        'status' => $request->status,
    ]);

    return redirect()->back()->with('success', 'Shift saved successfully!');
}


    public function update(Request $request, $id)
{
    $request->validate([
        'shift_name' => ['required', Rule::unique('shift_settings', 'shift_name')->ignore($id)],
        'start_time' => ['required', 'date_format:H:i'],
        'end_time' => ['required', 'date_format:H:i'],
    ]);

    $start = Carbon::createFromFormat('H:i', $request->start_time);
    $end = Carbon::createFromFormat('H:i', $request->end_time);

    if ($start->eq($end)) {
        return back()->withErrors([
            'start_time' => 'Start time and end time cannot be the same.',
            'end_time' => 'Start time and end time cannot be the same.',
        ])->withInput();
    }

    if ($end->lt($start)) {
        $end->addDay(); // handle overnight shift
    }

    if ($end->lessThanOrEqualTo($start)) {
        return back()->withErrors([
            'start_time' => 'Start time must be before end time.',
            'end_time' => 'End time must be after start time.',
        ])->withInput();
    }

    $shift = ShiftSetting::findOrFail($id);

    $shift->update([
        'shift_name' => $request->shift_name,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'description' => $request->description,
        'status' => $request->status,
    ]);

    return redirect()->route('shift.list')->with('success', 'Shift updated successfully!');
}


    public function edit($id)
    {
        $shift = ShiftSetting::findOrFail($id);

        return view('backend.attendance_module.shift_management.edit', compact('shift'));
    }

    public function create()
    {
        return view('backend.attendance_module.shift_management.create');
    }

    public function destroy($id)
    {
        $shift = ShiftSetting::findOrFail($id);
        $shift->delete();

        return redirect()->back()->with('success', 'Shift deleted successfully!');
    }
}
