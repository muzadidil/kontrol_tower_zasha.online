<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PpobTransaction;
use App\Models\Mitra;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        // Pakai order_trackings sebagai single source of truth untuk semua jenis order
        $totalOmzet = DB::table('order_trackings')
            ->where('status', 'selesai')
            ->sum('harga_jual');

        $totalCuanZasha = DB::table('order_trackings')
            ->where('status', 'selesai')
            ->sum('komisi_zasha');

        try {
            $totalProfitPpob = PpobTransaction::select(DB::raw('SUM(margin_zasha) as profit'))->value('profit') ?? 0;
        } catch (\Exception $e) {
            $totalProfitPpob = 0;
        }

        // Escrow held = order yg belum selesai/dibatalkan
        $totalDanaEscrow = DB::table('order_trackings')
            ->where('escrow_status', 'held')
            ->sum('harga_jual');

        $totalSaldoMengendap = Mitra::sum('saldo') + Pelanggan::sum('saldo');

        return view('admin.dashboard.finance', compact('totalOmzet', 'totalCuanZasha', 'totalProfitPpob', 'totalDanaEscrow', 'totalSaldoMengendap'));
    }

    public function deposit()
    {
        // Stats summary untuk full-width view
        $totalPelanggan = \App\Models\Pelanggan::count();
        $totalMitra     = Mitra::count();
        $totalSaldoPelanggan = \App\Models\Pelanggan::sum('saldo');
        $totalSaldoMitra     = Mitra::sum('saldo');

        return view('admin.finance.deposit', compact(
            'totalPelanggan', 'totalMitra', 'totalSaldoPelanggan', 'totalSaldoMitra'
        ));
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
