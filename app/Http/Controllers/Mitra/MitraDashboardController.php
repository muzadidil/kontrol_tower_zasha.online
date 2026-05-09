<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraDashboardController extends Controller
{
    private function mitra()
    {
        return Auth::guard('mitra')->user();
    }

    public function dashboard()
    {
        $mitra        = $this->mitra();
        $totalPesanan = DB::table('pesanan_mitra')->where('id_mitra', $mitra->id_mitra)->count();
        $pesananAktif = DB::table('pesanan_mitra')
                          ->where('id_mitra', $mitra->id_mitra)
                          ->whereNotIn('status_pesanan', ['Selesai', 'Batal', 'Dibatalkan'])
                          ->count();

        return view('mitra.dashboard', compact('mitra', 'totalPesanan', 'pesananAktif'));
    }

    public function pesanan()
    {
        $mitra   = $this->mitra();
        $pesanan = DB::table('pesanan_mitra')
                     ->where('id_mitra', $mitra->id_mitra)
                     ->orderByDesc('created_at')
                     ->get();

        return view('mitra.pesanan', compact('mitra', 'pesanan'));
    }

    public function saldo()
    {
        $mitra   = $this->mitra();
        $riwayat = DB::table('withdrawals')
                     ->where('mitra_id', $mitra->id_mitra)
                     ->orderByDesc('created_at')
                     ->get();

        return view('mitra.saldo', compact('mitra', 'riwayat'));
    }

    public function profil()
    {
        $mitra = $this->mitra();
        return view('mitra.profil', compact('mitra'));
    }
}
