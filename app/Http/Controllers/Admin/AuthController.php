<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            "email" => "required|email",
            "password" => "required|min:6"
        ]);
        $credentials = $request->only('email', 'password');

        if(Auth::attempt($credentials)){
            if(auth()->user()->is_admin){
                return redirect()->route('admin.dashboard')->with('success', 'Welcome Admin!');
            } else{
                Auth::logout();
                return redirect()->route('admin.login')->with('error', 'Unauthorized access.');
            }
        }
        return redirect()->route('admin.login')->with('error', 'Invalid login credentials.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }
}
