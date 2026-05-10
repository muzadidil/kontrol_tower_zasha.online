<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Kategori, Layanan};
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Support\Str;

class GameTopupController extends Controller
{
    public function __construct(private DigiflazzService $digiflazz) {}

    public function index(Request $request)
    {
        $query = Layanan::with('kategori');

        if ($request->filled('kategori')) {
            $query->whereHas('kategori', fn($q) => $q->where('kode', $request->kategori));
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('layanan', 'like', "%{$request->search}%")
                  ->orWhere('provider_id', 'like', "%{$request->search}%");
            });
        }

        $layanans  = $query->orderBy('kategori_id')->orderBy('harga')->paginate(50)->withQueryString();
        $kategoris = Kategori::orderBy('nama')->get();

        return view('admin.game-topup.index', compact('layanans', 'kategoris'));
    }

    public function importForm()
    {
        return view('admin.game-topup.import');
    }

    /**
     * Import produk dari Digiflazz pricelist, filter by brand,
     * simpan ke layanans pakai updateOrCreate (idempoten).
     */
    public function importProduk(Request $request)
    {
        $request->validate([
            'brand'      => 'required|string|max:100',
            'kode_kategori' => 'required|string|max:50|alpha_dash',
            'sub_nama'   => 'nullable|string|max:100',
            'tipe'       => 'required|in:game,voucher,pulsa',
            'server_id'  => 'required|in:0,1',
            'profit'     => 'required|integer|min:0|max:100',
            'thumbnail'  => 'nullable|string|max:500',
        ]);

        try {
            $produk = $this->digiflazz->pricelist();
        } catch (\RuntimeException $e) {
            Log::error('Game topup import error', ['message' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        }

        // Filter berdasarkan brand (case-sensitive)
        $filtered = collect($produk)->filter(fn($p) => isset($p['brand']) && $p['brand'] === $request->brand);

        if ($filtered->isEmpty()) {
            return back()->with('error', "Tidak ditemukan produk dengan brand \"{$request->brand}\". Cek penulisan persis seperti di Digiflazz.")->withInput();
        }

        DB::beginTransaction();
        try {
            $kategori = Kategori::updateOrCreate(
                ['kode' => $request->kode_kategori],
                [
                    'nama'      => $request->brand,
                    'sub_nama'  => $request->sub_nama,
                    'tipe'      => $request->tipe,
                    'server_id' => (int) $request->server_id,
                    'thumbnail' => $request->thumbnail,
                    'status'    => 'active',
                ]
            );

            $countNew = 0;
            $countUpdated = 0;

            foreach ($filtered as $item) {
                $hargaModal = (int) ($item['price'] ?? 0);
                $hargaJual  = (int) round($hargaModal * (1 + ($request->profit / 100)));
                $statusItem = ($item['buyer_product_status'] ?? false) === true ? 'available' : 'unavailable';

                $existed = Layanan::where('provider_id', $item['buyer_sku_code'])->exists();

                Layanan::updateOrCreate(
                    ['provider_id' => $item['buyer_sku_code']],
                    [
                        'kategori_id' => $kategori->id,
                        'layanan'     => $item['product_name'] ?? $item['buyer_sku_code'],
                        'provider'    => 'digiflazz',
                        'harga'       => $hargaJual,
                        'profit'      => $request->profit,
                        'status'      => $statusItem,
                    ]
                );

                $existed ? $countUpdated++ : $countNew++;
            }

            DB::commit();

            return redirect()->route('admin.game-topup.index', ['kategori' => $kategori->kode])
                ->with('success', "Import sukses: {$countNew} produk baru, {$countUpdated} diupdate untuk kategori \"{$kategori->nama}\".");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Game topup import db error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Error menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Sync harga produk existing (tanpa tambah produk baru).
     */
    public function syncHarga(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
        ]);

        try {
            $produk = $this->digiflazz->pricelist();
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $kategori = Kategori::findOrFail($request->kategori_id);
        $produkByKode = collect($produk)->keyBy('buyer_sku_code');
        $count = 0;

        foreach (Layanan::where('kategori_id', $kategori->id)->get() as $layanan) {
            $remote = $produkByKode->get($layanan->provider_id);
            if (! $remote) continue;

            $hargaModal = (int) ($remote['price'] ?? 0);
            $hargaJual  = (int) round($hargaModal * (1 + ($layanan->profit / 100)));
            $statusItem = ($remote['buyer_product_status'] ?? false) === true ? 'available' : 'unavailable';

            $layanan->update([
                'harga'  => $hargaJual,
                'status' => $statusItem,
            ]);
            $count++;
        }

        return back()->with('success', "Sync selesai: {$count} produk dari kategori \"{$kategori->nama}\" diupdate.");
    }

    public function destroyKategori(Kategori $kategori)
    {
        $nama = $kategori->nama;
        $kategori->delete();
        return redirect()->route('admin.game-topup.index')->with('success', "Kategori \"{$nama}\" dihapus beserta semua layanannya.");
    }
}
