<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan halaman login untuk panitia dan dewan juri.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isJudge()
                ? redirect()->route('judge.dashboard')
                : redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Memproses otentikasi login admin dan dewan juri (bisa menggunakan Email atau Username).
     */
    public function login(Request $request): RedirectResponse
    {
        // Mendukung input dari form dengan nama 'login' atau 'email'
        $loginInput = trim((string) ($request->input('login') ?: $request->input('email')));

        $request->merge(['login_identifier' => $loginInput]);

        $request->validate([
            'login_identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login_identifier.required' => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $request->input('password'),
        ];

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            session()->forget('url.intended');

            /** @var User $user */
            $user = Auth::user();

            if ($user->isJudge()) {
                return redirect()->route('judge.dashboard')
                    ->with('status', "Selamat datang, {$user->name}! Silakan lakukan penilaian karya Top 10.");
            }

            return redirect()->route('admin.dashboard')
                ->with('status', 'Selamat datang kembali di Dashboard Panitia BTIKP!');
        }

        return redirect()->route('admin.login')
            ->withInput($request->only('email', 'login', 'remember'))
            ->withErrors([
                'email' => 'Kombinasi akun (email/username) dan kata sandi yang Anda masukkan tidak sesuai.',
            ]);
    }

    /**
     * Logout pengguna (admin atau dewan juri).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('status', 'Anda telah berhasil keluar dari sistem.');
    }
}
