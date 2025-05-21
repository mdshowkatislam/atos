<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShiftSetting; 

class ShitController extends Controller
{
    public function shiftManage()
    {
        $databases = \DB::select("
        SELECT DISTINCT TABLE_SCHEMA
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_NAME = 'checkinout'
    ");

        $databaseNames = array_map(function ($db) {
            return $db->TABLE_SCHEMA;
        }, $databases);

        return view('backend.shift_management.index', compact('databaseNames'));
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
