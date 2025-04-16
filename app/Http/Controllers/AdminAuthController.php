<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function index(): View
    {
        return view('auth.login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (!Auth::guard('admin_users')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'auth' => 'Username atau password yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin_users')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
