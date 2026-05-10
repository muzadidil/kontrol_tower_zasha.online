<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\{Kategori, Layanan, Pelanggan};
use App\Services\PpobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};

class GameTopupController extends Controller
{
    public function __construct(private PpobService $ppobService) {}

    public function landing()
    {
        $kategoris = Kategori::where('status', 'active')->orderBy('tipe')->orderBy('nama')->get()->groupBy('tipe');
        return view('pelanggan.game-topup.landing', compact('kategoris'));
    }

    public function show(Kategori $kategori)
    {
        if ($kategori->status !== 'active') abort(404);
        $kategori->load('layanans');
        return view('pelanggan.game-topup.show', compact('kategori'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'layanan_id'   => 'required|exists:layanans,id',
            'user_id_game' => 'required|string|max:50',  // UID
            'server_id'    => 'nullable|string|max:20',  // Zone (kalau perlu)
        ]);

        $layanan = Layanan::with('kategori')->findOrFail($request->layanan_id);

        if ($layanan->status !== 'available' || $layanan->kategori->status !== 'active') {
            return back()->with('error', 'Produk tidak tersedia saat ini.');
        }

        // Build customer_no: UID atau UID|Zone
        $customerNo = $request->user_id_game;
        if ($layanan->kategori->butuhServerId() && $request->filled('server_id')) {
            $customerNo .= '|' . $request->server_id;
        }

        try {
            $pelangganId = auth('pelanggan')->id();

            // Hitung harga modal kira-kira (margin = harga jual / (1 + profit%))
            $hargaModal = $layanan->profit > 0
                ? (int) round($layanan->harga / (1 + ($layanan->profit / 100)))
                : $layanan->harga;

            $trx = $this->ppobService->createTransaction(
                [
                    'jenis_produk'  => 'game',
                    'nomor_tujuan'  => $customerNo,
                    'kode_produk'   => $layanan->provider_id,
                    'nama_produk'   => $layanan->layanan,
                    'harga_modal'   => $hargaModal,
                    'harga_jual'    => $layanan->harga,
                ],
                'pelanggan',
                $pelangganId
            );

            $this->ppobService->callDigiflazz($trx);

            return redirect()->route('pelanggan.ppob.show', $trx->id)
                ->with('success', 'Top-up sedang diproses, cek detail transaksi.');
        } catch (\RuntimeException $e) {
            Log::error('Game topup checkout error', ['message' => $e->getMessage(), 'pelanggan_id' => auth('pelanggan')->id()]);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
