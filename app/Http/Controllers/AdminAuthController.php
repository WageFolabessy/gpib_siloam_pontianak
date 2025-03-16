<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function index()
    {
        if (Auth::guard('admin_users')->check()) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::guard('admin_users')->attempt($credentials)) {
            return redirect()->intended('/dashboard');
        }

        return redirect()->back()->withErrors(['message' => 'username atau password salah']);
    }

    public function logout()
    {
        Auth::guard('admin_users')->logout();

        return redirect()->route('admin.login');
    }
}
