<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Mitra;

class MitraLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('mitra')->check()) {
            return redirect()->route('mitra.dashboard');
        }
        return view('auth.mitra-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nomor_wa' => ['required', 'string'],
            'password'  => ['required', 'string'],
        ]);

        // Normalisasi nomor WA ke format 62XXXXXXXXX
        $nomor = $request->nomor_wa;
        if (str_starts_with($nomor, '0')) {
            $nomor = substr($nomor, 1);
        }
        if (str_starts_with($nomor, '62')) {
            $nomor = substr($nomor, 2);
        }
        $nomor = '62' . $nomor;

        // Cari mitra berdasarkan nomor_wa
        $mitra = Mitra::where('nomor_wa', $nomor)->first();

        if (!$mitra || !Hash::check($request->password, $mitra->password)) {
            return back()->withErrors([
                'nomor_wa' => 'Nomor WhatsApp atau password salah.',
            ])->onlyInput('nomor_wa');
        }

        Auth::guard('mitra')->login($mitra, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->route('mitra.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('mitra')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('mitra.login');
    }
}
