<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    public function getUserRole(Request $request)
    {
        $user_role_type_id = $request->user_role_type_id;
        $allRole = Role::where('user_role_type_id', $user_role_type_id)->get();
        return response()->json($allRole);
    }

    public function index()
    {
        $data['user'] = User::with(['user_roles'])->where('id', '!=', 1)->latest()->get();
        $data['type'] = 'user';

        return view('backend.user.view-user', $data);
    }
    

    public function userAdd()
    {
        $data['roles'] = Role::where('id', '!=', 3)->get();
        return view('backend.user.add-user', $data);
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'mobile' => 'required|unique:users,mobile|digits:11',
            'password' => [
                'required',
                'confirmed',
                Password::min(8) // Minimum length of 6 characters
                    ->letters()    // Requires at least one letter
                    ->mixedCase()  // Requires mixed case (uppercase and lowercase)
                    ->numbers()    // Requires at least one number
                    ->symbols()    // Requires at least one special character
            ],
            'role_id' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png'
        ], [], [
            'mobile' => 'Mobile Number'
        ]);

        $data              = new User();
        $data->name        = $request->name;
        // $data->username    = $request->username;
        $data->email       = $request->email;
        $data->mobile       = $request->mobile;
        $data->password    = bcrypt($request->password);
        $data->status = $request->status ?? 0;

        if ($file = $request->file('image')) {
            $filename = date('Ymd') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/user'), $filename);
            $data->image = $filename;
        }
        $data->save();

        if ($request->role_id) {
            $user_data           = new UserRole();
            $user_data->user_id  = $data->id;
            $user_data->role_id  = $request->role_id;
            $user_data->save();
        }

      
        return redirect()->route('user')->with('success', 'Data Saved successfully');
    }

    public function userEdit($id)
    {
        $data['editData']   = User::with(['user_roles'])->where('id', '!=', 1)->findOrFail($id);
        // dd($data['editData']->toArray());
        $data['roles']      = Role::where('status', '1')->get();
        // dd($data['editData']);
        return view('backend.user.add-user', $data);
    }

    public function updateUser(Request $request, $id)
    {
        // dd($request);
        $request->validate([
            'name' => 'required',
            'image' => 'mimes:jpg,jpeg,png',
            'email' => 'required',
            'mobile' => 'required',
        ], [], [
            'mobile' => 'Mobile Number'
        ]);
      

        $data              = User::find($id);
        $data->name        = $request->name;
        $data->mobile       = $request->mobile;
        $data->email       = $request->email;

        $data->status = $request->status ?? 0;

        if ($file = $request->file('image')) {
            @unlink(public_path('upload/user/' . $data->image));
            $filename = date('Ymd') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/user'), $filename);
            $data->image = $filename;
        }



        $data->save();
        if ($request->role_id) {
            $user_data           = UserRole::where('user_id', $id)->first();
            if (!$user_data) {
                $user_data           = new UserRole();
                $user_data->user_id  = $data->id;
                $user_data->role_id  = $request->role_id;
                $user_data->save();
            } else {
                $user_data->role_id  = $request->role_id;
                $user_data->save();
            }
        }
     
        return redirect()->route('user')->with('success', 'Data Updated successfully');
    }

    public function deleteUser(Request $request)
    {
        $user = User::find($request->id);
        $user->delete();
        return redirect()->route('user')->with('success', 'Data Deleted successfully');
    }

    public function userStatus(Request $request)
    {
       
        $data = User::findOrFail($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 'Status Updated Successfully.']);
    }


}
