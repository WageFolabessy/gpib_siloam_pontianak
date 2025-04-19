<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteUser\ForgotPasswordRequest;
use App\Http\Requests\SiteUser\LoginRequest;
use App\Http\Requests\SiteUser\RegisterRequest;
use App\Http\Requests\SiteUser\ResetPasswordRequest;
use App\Http\Requests\SiteUser\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;

class UserAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('beranda'));
        }

        return back()->with('message', 'Email atau password yang diberikan salah.')
            ->onlyInput('email', 'remember');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showLinkRequestForm()
    {
        return view('pages.auth.request_password');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        switch ($status) {
            case Password::RESET_LINK_SENT:
                return back()->with('status', 'Berhasil! Link untuk mereset password Anda telah dikirim ke alamat email Anda. Silakan periksa kotak masuk (termasuk folder spam).');

            case Password::RESET_THROTTLED:
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Anda baru saja meminta link reset password. Silakan tunggu beberapa saat sebelum mencoba lagi.']);

            case Password::INVALID_USER:
            default:
                return back()->withInput($request->only('email'))
                    ->withErrors(['email' => 'Gagal mengirim link reset. Pastikan alamat email yang Anda masukkan sudah benar dan terdaftar.']);
        }
    }


    public function showResetForm(Request $request, $token = null)
    {
        return view('pages.auth.reset_password')->with(
            ['token' => $token, 'email' => $request->query('email')]
        );
    }

    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        return ($status == Password::PASSWORD_RESET)
            ? redirect()->route('login_jemaat')->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    public function showRegistrationForm()
    {
        return view('pages.auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        event(new Registered($user));
        Auth::guard('web')->login($user);
        return redirect(route('beranda'))->with('status', 'Registrasi berhasil! Selamat datang.');
    }

    public function showProfileForm(Request $request)
    {
        $user = $request->user('web');

        return view('pages.profil', compact('user'));
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user('web');

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
