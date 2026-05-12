<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpobTransaction;
use Illuminate\Http\Request;

class PpobController extends Controller
{
    public function index(Request $request)
    {
        $query = PpobTransaction::query();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('jenis')) $query->where('jenis_produk', $request->jenis);
        if ($request->filled('user_type')) $query->where('user_type', $request->user_type);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('digiflazz_ref', 'like', "%{$request->search}%")
                  ->orWhere('nomor_tujuan', 'like', "%{$request->search}%")
                  ->orWhere('nama_produk', 'like', "%{$request->search}%");
            });
        }

        $trxs = $query->latest()->paginate(25);

        // Statistik
        $stats = [
            'total_trx'        => PpobTransaction::count(),
            'sukses'           => PpobTransaction::where('status', 'sukses')->count(),
            'gagal'            => PpobTransaction::where('status', 'gagal')->count(),
            'pending'          => PpobTransaction::where('status', 'pending')->count(),
            'total_margin'     => PpobTransaction::where('status', 'sukses')->sum('margin_zasha'),
            'total_omzet'      => PpobTransaction::where('status', 'sukses')->sum('harga_jual'),
        ];

        return view('admin.ppob.index', compact('trxs', 'stats'));
    }

    public function show(PpobTransaction $trx)
    {
        return view('admin.ppob.show', compact('trx'));
    }

    /**
     * Update status PPOB secara manual oleh admin
     * (gagal -> sukses untuk reconciliation, pending -> gagal untuk force timeout, dll).
     */
    public function updateStatus(Request $request, PpobTransaction $trx)
    {
        $request->validate([
            'status' => 'required|in:pending,sukses,gagal',
        ]);

        $oldStatus = $trx->status;
        $trx->update(['status' => $request->status]);

        return back()->with('notif', "Status #{$trx->id} diubah dari {$oldStatus} ke {$request->status}.");
    }
}
