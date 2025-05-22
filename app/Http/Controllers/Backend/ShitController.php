<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ShiftSetting;
use Illuminate\Http\Request;

class ShitController extends Controller
{
    public function shiftManage()
    {
            $shift = ShiftSetting::first();
            if($shift){
                 return view('backend.shift_management.index', compact('shift'));
            }

        return view('backend.shift_management.index');
    }

    public function shiftAdd(Request $request)
    {
        $validated = $request->validate([
            'start_time' => ['required', 'date_format:H:i', 'before:end_time'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'start_time.before' => 'Start time must be before end time.',
            'end_time.after' => 'End time must be after start time.',
            'start_time.date_format' => 'Start time must be in HH:MM format.',
            'end_time.date_format' => 'End time must be in HH:MM format.',
        ]);

        $shift = ShiftSetting::first();

        if ($shift) {
          
            $shift->update([
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time']
            ]);
        } else {
            ShiftSetting::create([
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time']
            ]);
        }

        return redirect()->back()->with('success', 'Time Shit updated!');
    }
}
