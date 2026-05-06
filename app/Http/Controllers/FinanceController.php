<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PpobTransaction;
use App\Models\Mitra;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');

        $queryOrder = Order::where('status', 'Selesai');
        $queryOrderWfh = Order::where('status', '!=', 'Selesai'); // Assuming WFH logic is in orders

        // Apply filter logic here if needed...

        $totalOmzet = $queryOrder->sum('total_biaya');
        $totalCuanZasha = $queryOrder->sum('komisi_zasha');
        $totalProfitPpob = PpobTransaction::select(DB::raw('SUM(selling_price - price) as profit'))->value('profit');
        $totalDanaEscrow = $queryOrderWfh->sum('total_biaya');
        $totalSaldoMengendap = Mitra::sum('saldo') + Pelanggan::sum('saldo');

        return view('admin.dashboard.finance', compact('totalOmzet', 'totalCuanZasha', 'totalProfitPpob', 'totalDanaEscrow', 'totalSaldoMengendap'));
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
