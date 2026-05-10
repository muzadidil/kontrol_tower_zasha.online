<?php

namespace App\Http\Controllers\Auth;

use App\Enums\MitraKategori;
use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'password' => ['required', 'string'],
        ]);

        $no_wa = $this->normalizeWa($request->nomor_wa);
        $mitra = Mitra::where('no_wa', $no_wa)->first();

        if (! $mitra || ! Hash::check($request->password, $mitra->password)) {
            return back()->withErrors(['nomor_wa' => 'Nomor WhatsApp atau password salah.'])
                ->onlyInput('nomor_wa');
        }

        Auth::guard('mitra')->login($mitra, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->route('mitra.dashboard');
    }

    public function showRegisterForm()
    {
        if (Auth::guard('mitra')->check()) {
            return redirect()->route('mitra.dashboard');
        }

        $kategoris = collect(MitraKategori::cases())->map(fn($k) => [
            'value'         => $k->value,
            'label'         => $k->label(),
            'needsVehicle'  => $k->needsVehicle(),
            'needsLocation' => $k->needsLocation(),
            'needsSim'      => $k->needsSim(),
        ]);

        return view('auth.mitra-register', compact('kategoris'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'kategori_kode'  => 'required|in:TNG,WFH,JST,SVC',
            'nama_asli'      => 'required|string|max:100',
            'nama_panggilan' => 'required|string|max:50',
            'nomor_wa'       => 'required|string|min:9|max:15',
            'password'       => 'required|string|min:6|confirmed',
        ]);

        $no_wa = $this->normalizeWa($request->nomor_wa);

        if (Mitra::where('no_wa', $no_wa)->exists()) {
            return back()->withErrors(['nomor_wa' => 'Nomor WhatsApp ini sudah terdaftar sebagai mitra.'])->withInput();
        }

        $mitra = Mitra::create([
            'kategori_kode'     => $request->kategori_kode,
            'nama_asli'         => $request->nama_asli,
            'nama_panggilan'    => $request->nama_panggilan,
            'no_wa'             => $no_wa,
            'password'          => Hash::make($request->password),
            'status_mitra'      => 'offline',
            'status_verifikasi' => 'pending_document',
            'saldo'             => 0,
        ]);

        Auth::guard('mitra')->login($mitra);
        $request->session()->regenerate();

        return redirect()->route('mitra.dashboard')
            ->with('success', 'Pendaftaran berhasil! Lengkapi dokumen verifikasi untuk mulai menerima order.');
    }

    public function logout(Request $request)
    {
        Auth::guard('mitra')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('mitra.login');
    }

    private function normalizeWa(string $input): string
    {
        $wa = preg_replace('/\D/', '', $input);
        if (str_starts_with($wa, '0'))  $wa = substr($wa, 1);
        if (str_starts_with($wa, '62')) $wa = substr($wa, 2);
        return '62' . $wa;
    }
}
