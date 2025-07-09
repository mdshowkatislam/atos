<?php

namespace App\Http\Controllers\Backend\AttendanceModule;

use App\Http\Controllers\Controller;
use App\Models\ShiftSetting;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {dd('hi');
            $shift = ShiftSetting::first();
            if($shift){
                 return view('backend.shift_management.index', compact('shift'));
            }

        return view('backend.shift_management.index');
    }
    public function add()
    {
dd('hi');
    }
    public function edit($id)
    {
dd('hi');
    }
    public function store(Request $request)
    {
dd('hi');
    }
    public function update(Request $request,$id)
    {
dd('hi');
    }
    public function destroy($id)
    {
dd('hi');
    }
}