<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('backend.auth.login');
    }

    public function postLogin(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user_exists = User::where('email', $request->email)->first();

        if ($request->email == 'admin@gmail.com') {
            if (Auth::attempt($request->only(['email', 'password']), $request->get('remember'))) {
               
                // logData($primary_id = Auth::id(), $url = url()->previous(), $old_data = Auth::user(), $new_data = Auth::user(), $action = 'login', $created_by = Auth::id(), $updated_by = null);
               
                // return redirect()->intended('/home');            
                return redirect()->intended('/home');

            } else {
                dd('admin login failed');
                return redirect()->back()->with('error', 'Wrong Password!');
            }
        }

        if (!$user_exists) {
            return redirect()->back()->with('error', 'User not Found!');
        }
        if (Hash::check($user_exists->password, $request->password)) {
            return redirect()->back()->with('error', 'Wrong Password!');
        }

        $token = mt_rand(100000, 999999);
        $twofactorauthentication = [
            'token' => $token,
            'email' => $request->email
        ];
        // Store the token in the user's session
        $request->session()->put('2fa_token', $twofactorauthentication);

        $sms = $this->sendTokenViaSms($request->email, $token);

        if (@$sms['statusCode'] != '200') {
            return redirect()->back()->with('error', @$sms['message']);
        }

        return redirect()->route('verify.token');
    }

    // public function showVerifyTokenForm()
    // {
    //     return view('backend.auth.validate');
    // }

    // public function postVerifyToken(Request $request)
    // {
    //     $request->validate([
    //         'token' => 'required|digits:6',
    //     ]);
    //     // dd($request->session()->get('2fa_token.token'));
    //     if ($request->token && ($request->session()->get('2fa_token.token') == $request->token)) {
    //         $user = User::where('email', $request->session()->get('2fa_token.email'))->first();
    //         if ($user) {
    //             Auth::login($user);
    //         }
    //         $request->session()->forget('2fa_token');
    //         return redirect()->intended('/home');
    //     } else {
    //         return redirect()->back()->withErrors(['token' => 'Invalid token.']);
    //     }
    // }

    // private function sendTokenViaSms($email, $token)
    // {
    //     // Fetch the user's phone number based on their email
    //     $user = User::where('email', $email)->first();
    //     $phoneNumber = $user->mobile;
    //     $phoneNumber = substr(preg_replace('/[^0-9]/', '', $phoneNumber), -11);

    //     $request_array = [
    //         "username" => "BUPadmin",
    //         "password" => "Bup@Api&20",
    //         "apicode" => "1",
    //         "msisdn" => $phoneNumber,
    //         "countrycode" => "880",
    //         "cli" => "BUP",
    //         "messagetype" => "1",
    //         "message" => "Your BUP Login OTP is " . $token . " - BUP Support.",
    //         "messageid" => "0"
    //     ];

    //     $sms = Http::withOptions(['verify' => false])->withHeaders([
    //         'Content-Type' => 'application/json'
    //     ])
    //         ->send('POST', 'https://gpcmp.grameenphone.com/ecmapigw/webresources/ecmapigw.v2', [
    //             'body' => json_encode($request_array)
    //         ])->json();

    //     return $sms;
    // }

    public function logOut(Request $request)
    {
        // logData($primary_id = Auth::id(), $url = url()->previous(), $old_data = Auth::user(), $new_data = '', $action = 'logout', $created_by = Auth::id(), $updated_by = null);
        auth()->logout();
        $request->session()->invalidate();

        return redirect('/login');
    }
}
