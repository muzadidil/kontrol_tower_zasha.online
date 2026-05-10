<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\PpobTransaction;
use App\Services\PpobService;
use Illuminate\Http\Request;

class PpobController extends Controller
{
    public function __construct(private PpobService $ppobService) {}

    public function index()
    {
        $trxs = PpobTransaction::where('user_type', 'pelanggan')
            ->where('user_id', auth('pelanggan')->id())
            ->latest()->paginate(20);

        return view('pelanggan.ppob.index', compact('trxs'));
    }

    public function form()
    {
        return view('pelanggan.ppob.form');
    }

    public function transaksi(Request $request)
    {
        $request->validate([
            'jenis_produk' => 'required|in:pulsa,paket_data,token_listrik',
            'nomor_tujuan' => 'required|string',
            'kode_produk'  => 'required|string',
            'nama_produk'  => 'required|string',
            'harga_modal'  => 'required|numeric',
            'harga_jual'   => 'required|numeric',
        ]);

        try {
            $trx = $this->ppobService->createTransaction(
                $request->only(['jenis_produk', 'nomor_tujuan', 'kode_produk', 'nama_produk', 'harga_modal', 'harga_jual']),
                'pelanggan',
                auth('pelanggan')->id()
            );

            // Panggil Digiflazz (async di production, sync di sini)
            $this->ppobService->callDigiflazz($trx);

            return redirect()->route('pelanggan.ppob.index')->with('success', 'Transaksi sedang diproses.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(PpobTransaction $trx)
    {
        if ($trx->user_type !== 'pelanggan' || (int) $trx->user_id !== auth('pelanggan')->id()) abort(403);
        return view('pelanggan.ppob.show', compact('trx'));
    }
}
