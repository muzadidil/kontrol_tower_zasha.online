<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('pelanggan')->check()) {
            return redirect()->route('pelanggan.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'no_wa'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $no_wa = $this->normalizeWa($request->no_wa);

        $credentials = [
            'no_wa'    => $no_wa,
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

    public function showRegisterForm()
    {
        if (Auth::guard('pelanggan')->check()) {
            return redirect()->route('pelanggan.dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_wa'          => 'required|string|min:9|max:15',
            'password'       => 'required|string|min:6|confirmed',
        ]);

        $no_wa = $this->normalizeWa($request->no_wa);

        if (Pelanggan::where('no_wa', $no_wa)->exists()) {
            return back()->withErrors(['no_wa' => 'Nomor WhatsApp ini sudah terdaftar.'])->withInput();
        }

        $pelanggan = Pelanggan::create([
            'nama_pelanggan'      => $request->nama_pelanggan,
            'no_wa'               => $no_wa,
            'password'            => Hash::make($request->password),
            'kode_zasha'          => 'PLG-' . strtoupper(Str::random(6)),
            'saldo'               => 0,
            'status_verifikasi'   => 0,
            'is_profile_complete' => false,
        ]);

        Auth::guard('pelanggan')->login($pelanggan);
        $request->session()->regenerate();

        return redirect()->route('pelanggan.dashboard')
            ->with('success', 'Selamat datang di Zasha! Lengkapi profil untuk mulai memesan.');
    }

    public function logout(Request $request)
    {
        Auth::guard('pelanggan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function normalizeWa(string $input): string
    {
        $wa = preg_replace('/\D/', '', $input);
        if (str_starts_with($wa, '0'))  $wa = substr($wa, 1);
        if (str_starts_with($wa, '62')) $wa = substr($wa, 2);
        return '62' . $wa;
    }
}
