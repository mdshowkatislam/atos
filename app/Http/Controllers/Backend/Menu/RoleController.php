<?php

namespace App\Http\Controllers\Backend\Menu;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;


class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
    	return view('backend.user.view-user-role')->with('roles', $roles);
    }

    public function storeRole(Request $request)
    {
       
        // return $request->all();
        $this->validate($request, [
            'name' => 'required|unique:roles,name'
        ],[
            'name.required' => 'The Role Name field is required',
            'unique'    => 'This Role is already used'
        ]);

        $roleData = new Role;
        $roleData->name = $request->name;
        $roleData->description = $request->description;
        //$roleData->working_area = $request->working_area;
        $roleData->save();
        $request->session()->flash('success','Role Name Save Successfully');
        return redirect()->back();
    }

    public function getRole(Request $request)
    {
        $roleId = $request->input('id');
        $roleData = Role::find($roleId);
        return response()->json($roleData);
    }

    public function updateRole(Request $request, $id)
    {
        // dd($request,$id);

        $roleData = Role::find($id);
        $this->validate($request, [
            'name_update' => 'required|unique:roles,name,'.$roleData->id
        ],[
            'name_update.required' => 'The Role Name field is required',
            'unique'    => 'This Role is already used'
        ]);

        $roleData->name = $request->name_update;
        $roleData->description = $request->description;
        //$roleData->working_area = $request->working_area;
        $roleData->save();
        $request->session()->flash('success','Role Name Updated Successfully');
        return redirect()->back();
    }

    public function deleteRole(Request $request)
    {
        $id=$request->id;
        Role::find($id)->delete();
        $request->session()->flash('success','Role Name Updated Successfully');
        return redirect()->back();
    }
}
