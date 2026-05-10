<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PpobTransaction;
use App\Models\Mitra;
use App\Models\Pelanggan;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $totalOmzet = DB::table('pesanan_mitra')
            ->where('status_pesanan', 'Selesai')
            ->sum('total_pesanan');

        $totalCuanZasha = DB::table('pesanan_jastip')
            ->where('status_jastip', 'Selesai')
            ->sum(DB::raw('(ongkir * 0.1) + (total_admin_lokasi * 0.5)'));

        $totalProfitPpob = PpobTransaction::select(DB::raw('SUM(selling_price - price) as profit'))->value('profit') ?? 0;

        $totalDanaEscrow = DB::table('pesanan_mitra')
            ->whereNotIn('status_pesanan', ['Selesai', 'Batal', 'Dibatalkan'])
            ->sum('total_pesanan');

        $totalSaldoMengendap = Mitra::sum('saldo') + Pelanggan::sum('saldo');
        $gmaps_api_key       = Setting::get('google_maps_api_key', '');

        return view('admin.dashboard.finance', compact('totalOmzet', 'totalCuanZasha', 'totalProfitPpob', 'totalDanaEscrow', 'totalSaldoMengendap', 'gmaps_api_key'));
    }

    public function deposit()
    {
        return view('admin.finance.deposit');
    }

    public function storeDeposit(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'tipe_user' => 'required',
            'jumlah_nominal' => 'required|numeric',
        ]);

        if ($request->tipe_user == 'Mitra') {
            $user = Mitra::findOrFail($request->user_id);
        } else {
            $user = Pelanggan::findOrFail($request->user_id);
        }

        $user->saldo += $request->jumlah_nominal;
        $user->save();

        return redirect()->route('admin.finance.deposit')->with('success', 'Deposit berhasil ditambahkan.');
    }
}
