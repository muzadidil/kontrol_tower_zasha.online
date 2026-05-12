<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\NotifHelper;
use App\Models\OrderTracking;

class PelangganController extends Controller
{
    public function index()
    {
        $user = auth('pelanggan')->user();
        if (!$user) return redirect()->route('login');

        // Categories diambil dari roles yang aktif (mengganti tabel kategori_pekerjaan legacy).
        // Map field agar kompatibel dengan view yg masih pakai $kat->id_kategori, nama_kategori, svg_kategori.
        $categories = \App\Models\Role::where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(function ($role) {
                return (object) [
                    'id_kategori'  => $role->id,
                    'nama_kategori' => str_replace('Mitra ', '', $role->name), // "Mitra TNG" -> "TNG"
                    'svg_kategori' => '<i class="bi ' . ($role->icon ?: 'bi-shield-fill') . '" style="font-size:1.5rem; color:' . ($role->icon_color ?: '#005aa9') . ';"></i>',
                    'satuan'       => 'Jam',
                    'warna_tema'   => 'primary',
                ];
            });

        $unread_notif = NotifHelper::unreadCount($user->id_pelanggan);

        return view('pelanggan.dashboard', compact('user', 'categories', 'unread_notif'));
    }

    public function dompet()
    {
        $id_p      = auth('pelanggan')->id();
        $pelanggan = DB::table('pelanggans')->where('id_pelanggan', $id_p)->first();
        $riwayat   = DB::table('dompet_pelanggan')->where('id_pelanggan', $id_p)->orderBy('waktu_request', 'desc')->get();
        return view('pelanggan.dompet', compact('pelanggan', 'riwayat'));
    }

    public function topup(Request $request)
    {
        $request->validate([
            'nominal'     => 'required|numeric|min:10000|max:10000000',
            'bank_tujuan' => 'required|in:DANA,BCA',
        ]);

        $id_p = auth('pelanggan')->id();

        // Bersihkan format nominal (hapus titik, koma, spasi) lalu cast ke integer
        $rawNominal = $request->input('nominal');
        $nominal    = (int) preg_replace('/[^0-9]/', '', $rawNominal);

        // Pastikan nominal valid setelah parsing
        if ($nominal < 10000) {
            return back()->withErrors(['nominal' => 'Nominal minimal Rp 10.000'])->withInput();
        }

        // Generate kode unik 3 digit (100-999) untuk identifikasi transfer
        $kode_unik = rand(100, 999);

        // Hitung total transfer = nominal + kode_unik
        $total_transfer = $nominal + $kode_unik;

        DB::table('dompet_pelanggan')->insert([
            'id_pelanggan'   => (int) $id_p,
            'nominal'        => $nominal,
            'kode_unik'      => $kode_unik,
            'total_transfer' => $total_transfer,
            'bank_tujuan'    => $request->input('bank_tujuan'),
            'status'         => 'pending',
            'waktu_request'  => now(),
        ]);

        NotifHelper::kirim(
            $id_p,
            'Request Top-Up Diterima',
            'Request top-up sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' sedang diproses. Transfer tepat Rp ' . number_format($total_transfer, 0, ',', '.') . ' agar otomatis terdeteksi.',
            'topup',
            route('pelanggan.dompet')
        );

        return back()
            ->with('notif_topup', 'sukses')
            ->with('data_nominal', $nominal)
            ->with('data_kode_unik', $kode_unik)
            ->with('data_transfer', $total_transfer)
            ->with('data_bank', $request->input('bank_tujuan'));
    }

    public function invoice($id)
    {
        $d = DB::table('pesanan as p')
            ->join('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->join('pelanggan as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->leftJoin('alamats as a', function ($join) {
                $join->on('a.id_pelanggan', '=', 'pl.id_pelanggan')->where('a.is_utama', 1);
            })
            ->select(
                'p.*',
                'm.nama_asli as nama_mitra',
                'pl.nama_pelanggan',
                DB::raw('COALESCE(a.alamat_lengkap, "Alamat belum diatur") as alamat_pelanggan')
            )
            ->where('p.id_pesanan', $id)
            ->first();
        return view('pelanggan.invoice', compact('d'));
    }

    public function cekStatus($id)
    {
        $status = DB::table('pesanan')->where('id_pesanan', $id)->value('status_pesanan');
        return response()->json(['status' => $status ?? 'tidak_ditemukan']);
    }

    public function katalog(Request $request, $id_kategori)
    {
        // $id_kategori sekarang = role_id (karena dashboard pelanggan pass role->id sebagai id_kategori)
        $role = \App\Models\Role::where('id', $id_kategori)->where('is_active', true)->first();
        if (!$role) abort(404);

        // Pseudo-object kategori untuk kompatibilitas view
        $kategori = (object) [
            'id_kategori'   => $role->id,
            'nama_kategori' => str_replace('Mitra ', '', $role->name),
        ];

        $sort      = $request->get('sort', 'bintang');
        $is_jastip = $role->name === 'Mitra JST';

        // Filter mitra by role_id
        $query = DB::table('mitra as m')
            ->leftJoin('pesanan as p', 'm.id_mitra', '=', 'p.id_mitra')
            ->select(
                'm.id_mitra',
                'm.nama_panggilan as nama_mitra',
                'm.foto_mitra',
                'm.status_mitra',
                'm.status_online',
                DB::raw('COALESCE(m.tarif_per_jam, 0) as tarif_per_jam'),
                DB::raw('0 as jarak'),
                DB::raw('"Jam" as satuan_tarif'),
                DB::raw('COALESCE(AVG(p.rating), 0) as rating_rata'),
                DB::raw('COUNT(p.id_pesanan) as total_order')
            )
            ->where('m.role_id', $role->id)
            ->groupBy('m.id_mitra', 'm.nama_panggilan', 'm.foto_mitra', 'm.status_mitra', 'm.status_online', 'm.tarif_per_jam');

        if ($sort == 'orderan') {
            $query->orderByDesc('total_order');
        } elseif ($sort == 'bintang') {
            $query->orderByDesc('rating_rata');
        } else {
            $query->orderBy('m.id_mitra');
        }

        $mitras = $query->get();

        return view('pelanggan.katalog', [
            'kategori'    => $kategori,
            'mitras'      => $mitras,
            'nama_kat'    => $kategori->nama_kategori ?? 'Katalog',
            'id_kategori' => $id_kategori,
            'sort'        => $sort,
            'is_jastip'   => $is_jastip,
        ]);
    }

    public function review($id_order)
    {
        $order = DB::table('pesanan as p')
            ->leftJoin('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->select('p.id_pesanan as id_order', 'm.nama_panggilan as nama_mitra', 'p.id_mitra')
            ->where('p.id_pesanan', $id_order)
            ->where('p.id_pelanggan', auth('pelanggan')->id())
            ->first();
        return view('pelanggan.review', compact('order'));
    }

    public function kirimReview(Request $request)
    {
        $request->validate([
            'id_order' => 'required',
            'id_mitra' => 'required',
            'rating'   => 'required|integer|min:1|max:5',
            'ulasan'   => 'nullable|string|max:500',
        ]);

        DB::table('pesanan')
            ->where('id_pesanan', $request->id_order)
            ->where('id_pelanggan', auth('pelanggan')->id())
            ->update([
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
            ]);

        return redirect()->route('pelanggan.riwayat.index')->with('success', 'Terima kasih! Ulasan berhasil dikirim.');
    }

    public function detailMitra($id)
    {
        $mitra = DB::table('mitra as m')
            ->leftJoin('roles as r', 'm.role_id', '=', 'r.id')
            ->select('m.*', 'r.name as nama_kategori', DB::raw('"Jam" as satuan_tarif'))
            ->where('m.id_mitra', $id)
            ->first();

        if (!$mitra) abort(404);

        $rating_rata = DB::table('pesanan')
            ->where('id_mitra', $id)
            ->whereNotNull('rating')
            ->where('rating', '>', 0)
            ->avg('rating') ?? 0;

        $total_order = DB::table('pesanan')->where('id_mitra', $id)->count();

        $ulasans = DB::table('pesanan as p')
            ->join('pelanggans as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->select(
                'p.rating',
                'p.ulasan',
                DB::raw('COALESCE(pl.nama_panggilan, pl.nama_pelanggan, "Pelanggan") as nama_pelanggan'),
                'p.id_pesanan'
            )
            ->where('p.id_mitra', $id)
            ->whereNotNull('p.ulasan')
            ->orderBy('p.id_pesanan', 'desc')
            ->limit(5)
            ->get();

        $alamats = auth('pelanggan')->check()
            ? DB::table('alamats')->where('id_pelanggan', auth('pelanggan')->id())->orderByDesc('is_utama')->get()
            : collect();

        return view('pelanggan.detail-mitra', compact('mitra', 'rating_rata', 'total_order', 'ulasans', 'alamats'));
    }

    public function detailJastip($id)
    {
        // Driver jastip = mitra dengan kategori_kode = 'JST'
        $driver = DB::table('mitra')
            ->where('id_mitra', $id)
            ->where('kategori_kode', 'JST')
            ->first();
        if (! $driver) abort(404);

        return view('pelanggan.detail-jastip', compact('driver'));
    }

    public function simpanPesanan(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id();
        $mitra        = DB::table('mitra')->where('id_mitra', $request->id_mitra)->first();

        if (! $mitra) {
            return back()->with('error', 'Mitra tidak ditemukan.');
        }

        if ($mitra->status_online !== 'online') {
            $msg = match($mitra->status_online) {
                'offline' => 'Mitra sedang offline dan tidak menerima order.',
                'sibuk' => 'Mitra sedang dalam pengerjaan order lain.',
                default => 'Mitra tidak tersedia saat ini.',
            };
            return back()->with('error', $msg);
        }

        if ($request->input('tipe') === 'jastip') {
            return $this->simpanPesananJastip($request, $id_pelanggan, $mitra);
        }

        $total_pesanan = ($mitra->tarif_per_jam * $request->durasi) + 5000;

        $id_pesanan = DB::table('pesanan')->insertGetId([
            'id_pelanggan'    => $id_pelanggan,
            'id_mitra'        => $request->id_mitra,
            'id_kategori'     => $mitra->id_kategori,
            'tanggal_pesanan' => now(),
            'total_pesanan'   => $total_pesanan,
            'status_pesanan'  => 'Pending',
        ]);

        // Create order tracking untuk polling mitra
        try {
            OrderTracking::create([
                'mitra_id'      => (int) $request->id_mitra,
                'order_type'    => 'pesanan',
                'order_id'      => (int) $id_pesanan,
                'pelanggan_id'  => (int) $id_pelanggan,
                'status'        => 'pending',
                'harga_jual'    => $total_pesanan,
                'harga_modal'   => round($total_pesanan * 0.95, 2),
                'komisi_zasha'  => round($total_pesanan * 0.05, 2),
                'escrow_status' => 'held',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create OrderTracking', [
                'message'      => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
                'pesanan_id'   => $id_pesanan,
                'pelanggan_id' => $id_pelanggan,
                'mitra_id'     => $request->id_mitra,
            ]);
            // Jangan throw — order tetap berhasil meski tracking gagal
        }

        NotifHelper::kirim(
            $id_pelanggan,
            'Pesanan Berhasil Dibuat',
            'Pesanan #' . $id_pesanan . ' ke mitra ' . $mitra->nama_panggilan . ' telah dibuat dan sedang menunggu konfirmasi.',
            'pesanan',
            route('pelanggan.riwayat.index')
        );

        return redirect()->route('pelanggan.invoice', $id_pesanan);
    }

    /**
     * Buat order jastip baru di tabel jastip_orders.
     * Form sederhana — koordinat & breakdown ongkos diisi placeholder
     * (akan dilengkapi mitra saat menerima order).
     */
    private function simpanPesananJastip(Request $request, int $id_pelanggan, object $mitra)
    {
        $request->validate([
            'lokasi_asal'        => 'required|string|max:255',
            'daftar_belanja'     => 'required|string|max:2000',
            'total_harga_barang' => 'nullable|numeric|min:0',
            'metode_pembayaran'  => 'nullable|in:COD,Transfer,Saldo',
        ]);

        $estimasiBarang = (int) ($request->input('total_harga_barang') ?? 0);
        $codEligible    = ($request->input('metode_pembayaran') === 'COD');

        try {
            $orderId = DB::transaction(function () use ($request, $id_pelanggan, $mitra, $estimasiBarang, $codEligible) {
                $now = now();

                // Escrow: kunci pelanggan, cek saldo, decrement.
                // Skip cek kalau estimasi=0 — pelanggan belum tahu total, mitra akan top-up actual nanti.
                $pelangganLocked = \App\Models\Pelanggan::where('id_pelanggan', $id_pelanggan)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($estimasiBarang > 0) {
                    if ($pelangganLocked->saldo < $estimasiBarang) {
                        throw new \RuntimeException('Saldo tidak cukup untuk escrow order ini.');
                    }
                    $pelangganLocked->decrement('saldo', $estimasiBarang);
                }

                $orderId = DB::table('jastip_orders')->insertGetId([
                    'mitra_id'              => $mitra->id_mitra,
                    'order_code'            => 'JST-' . strtoupper(Str::random(8)),
                    'pelanggan_id'          => $id_pelanggan,
                    'delivery_address'      => $request->lokasi_asal,
                    'delivery_lat'          => 0,
                    'delivery_lng'          => 0,
                    'total_jarak_km'        => 0,
                    'total_stops'           => 1,
                    'tarif_per_km'          => 0,
                    'biaya_stop'            => 0,
                    'ongkos_jasa'           => 0,
                    'komisi_zasha'          => 0,
                    'pendapatan_mitra'      => 0,
                    'estimasi_total_barang' => $estimasiBarang,
                    'actual_total_barang'   => 0,
                    'saldo_mitra_snapshot'  => $mitra->saldo ?? 0,
                    'cod_eligible'          => $codEligible,
                    'status'                => 'menunggu_mitra',
                    'mitra_notified_at'     => $now,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ]);

                $stopId = DB::table('jastip_stops')->insertGetId([
                    'jastip_order_id'    => $orderId,
                    'urutan'             => 1,
                    'nama_lokasi'        => $request->lokasi_asal,
                    'alamat_lokasi'      => $request->lokasi_asal,
                    'lat'                => 0,
                    'lng'                => 0,
                    'jarak_dari_prev_km' => 0,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ]);

                DB::table('jastip_order_items')->insert([
                    'jastip_order_id' => $orderId,
                    'jastip_stop_id'  => $stopId,
                    'urutan'          => 1,
                    'nama_barang'     => 'Daftar belanjaan (lihat catatan)',
                    'harga_perkiraan' => $estimasiBarang,
                    'is_checked'      => false,
                    'catatan'         => $request->daftar_belanja,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);

                // Create order tracking untuk polling mitra
                // Gunakan estimasi harga barang sebagai harga_jual (akan diupdate nanti)
                try {
                    OrderTracking::create([
                        'mitra_id'      => (int) $mitra->id_mitra,
                        'order_type'    => 'jastip',
                        'order_id'      => (int) $orderId,
                        'pelanggan_id'  => (int) $id_pelanggan,
                        'status'        => 'pending',
                        'harga_jual'    => $estimasiBarang > 0 ? $estimasiBarang : 1000,
                        'harga_modal'   => $estimasiBarang > 0 ? round($estimasiBarang * 0.95, 2) : 950,
                        'komisi_zasha'  => $estimasiBarang > 0 ? round($estimasiBarang * 0.05, 2) : 50,
                        'escrow_status' => 'held',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create OrderTracking (jastip)', [
                        'message'      => $e->getMessage(),
                        'file'         => $e->getFile(),
                        'line'         => $e->getLine(),
                        'order_id'     => $orderId,
                        'pelanggan_id' => $id_pelanggan,
                        'mitra_id'     => $mitra->id_mitra,
                    ]);
                    // Jangan throw — order tetap berhasil meski tracking gagal
                }

                return $orderId;
            });

            NotifHelper::kirim(
                $id_pelanggan,
                'Order Jastip Dibuat',
                'Order jastip #' . $orderId . ' ke ' . $mitra->nama_panggilan . ' telah dibuat dan menunggu konfirmasi mitra.',
                'pesanan',
                route('pelanggan.riwayat.index')
            );

            return redirect()->route('pelanggan.riwayat.index')
                ->with('success', 'Order jastip berhasil dibuat. Menunggu konfirmasi mitra.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            Log::error('Simpan order jastip gagal', [
                'message'      => $e->getMessage(),
                'pelanggan_id' => $id_pelanggan,
                'mitra_id'     => $mitra->id_mitra,
                'lokasi_asal'  => $request->lokasi_asal,
            ]);
            return back()->with('error', 'Gagal menyimpan order jastip. Silakan coba lagi.');
        }
    }

    public function notifikasi()
    {
        $id_p = auth('pelanggan')->id();

        $notifikasi = DB::table('notifikasi')
            ->where('id_pelanggan', $id_p)
            ->orderByDesc('created_at')
            ->get();

        NotifHelper::tandaiSemuaDibaca($id_p);

        return view('pelanggan.notifikasi', compact('notifikasi'));
    }
}
