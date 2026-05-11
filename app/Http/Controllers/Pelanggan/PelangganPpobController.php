<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Layanan;
use App\Services\PpobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PelangganPpobController extends Controller
{
    private $ppobService;

    public function __construct(PpobService $ppobService)
    {
        $this->middleware('auth:pelanggan');
        $this->ppobService = $ppobService;
    }

    public function kategori($tipe)
    {
        $kategoris = Kategori::where('tipe', $tipe)
            ->where('status', 'active')
            ->withCount('layanans')
            ->get();

        $judul = match ($tipe) {
            'pulsa' => 'Pulsa',
            'token' => 'Token Listrik',
            'game' => 'Game',
            'ewallet' => 'E-Money',
            default => 'Produk'
        };

        return view('pelanggan.ppob.kategori', compact('kategoris', 'tipe', 'judul'));
    }

    public function produk(Kategori $kategori)
    {
        $layanans = Layanan::where('kategori_id', $kategori->id)
            ->where('status', 'available')
            ->orderBy('harga', 'asc')
            ->get();

        $user = Auth::guard('pelanggan')->user();

        return view('pelanggan.ppob.produk', compact('kategori', 'layanans', 'user'));
    }

    public function checkout(Request $request)
    {
        $rules = [
            'layanan_id' => 'required|exists:layanans,id',
            'nomor_tujuan' => 'required|string|min:3|max:50',
        ];

        $layanan = Layanan::findOrFail($request->layanan_id);
        $kategori = $layanan->kategori;

        if ($kategori->server_id == 1) {
            $rules['zone_id'] = 'required|string|min:1|max:50';
        }

        $request->validate($rules);

        $user = Auth::guard('pelanggan')->user();

        if ($user->is_verif == 0) {
            return back()->with('error', 'Akun belum terverifikasi. Lengkapi profil terlebih dahulu.');
        }

        try {
            $hargaModal = ceil($layanan->harga / (1 + $layanan->profit / 100));
            $nomor = $request->nomor_tujuan;

            if ($kategori->server_id == 1) {
                $nomor = $request->nomor_tujuan . '|' . $request->zone_id;
            }

            $data = [
                'jenis_produk' => $kategori->tipe,
                'nomor_tujuan' => $nomor,
                'kode_produk' => $layanan->provider_id,
                'nama_produk' => $layanan->layanan,
                'harga_modal' => $hargaModal,
                'harga_jual' => $layanan->harga,
            ];

            $trx = $this->ppobService->createTransaction($data, 'pelanggan', $user->id_pelanggan);
            $this->ppobService->callDigiflazz($trx);

            return redirect()->route('pelanggan.ppob.show', $trx->id)
                ->with('success', 'Pesanan berhasil diproses! Status akan diupdate segera.');
        } catch (\RuntimeException $e) {
            Log::error('PPOB checkout error', ['message' => $e->getMessage(), 'user_id' => $user->id_pelanggan]);
            return back()->with('error', $e->getMessage());
        }
    }
}
