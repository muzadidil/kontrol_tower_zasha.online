<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'no_wa' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $normalized_wa = $request->no_wa;

        // Strip leading '0'
        if (str_starts_with($normalized_wa, '0')) {
            $normalized_wa = substr($normalized_wa, 1);
        }

        // If it starts with '62', strip it
        if (str_starts_with($normalized_wa, '62')) {
            $normalized_wa = substr($normalized_wa, 2);
        }

        // Prepend '62' to ensure consistency
        $normalized_wa = '62' . $normalized_wa;

        $credentials = [
            'no_wa' => $normalized_wa,
            'password' => $request->password,
        ];

        if (Auth::guard('pelanggan')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('pelanggan.dashboard'))
                ->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors([
            'no_wa' => 'Nomor WhatsApp atau password salah.',
        ])->onlyInput('no_wa');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('pelanggan')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
