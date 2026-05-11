<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\NotifHelper;

class RiwayatController extends Controller
{
    private function getBadgeColor($status)
    {
        $s = strtolower(trim($status));
        if (in_array($s, ['menunggu', 'pending'])) return 'bg-warning text-dark';
        if (in_array($s, ['proses', 'berjalan', 'aktif', 'diterima', 'belanja', 'pengiriman'])) return 'bg-primary text-white';
        if ($s == 'selesai') return 'bg-success text-white';
        return 'bg-secondary text-white';
    }

    public function index(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id(); // Menggunakan auth('pelanggan')->id()
        $status_filter = $request->get('status', 'semua');

        // Query Jasa (Pesanan Mitra)
        $query_jasa = DB::table('pesanan_mitra as p')
            ->leftJoin('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->leftJoin('kategori_pekerjaan as k', 'm.id_kategori', '=', 'k.id_kategori')
            ->select('p.*', 'm.nama_panggilan as nama_mitra', 'm.foto_mitra', 'k.nama_kategori')
            ->where('p.id_pelanggan', $id_pelanggan)
            ->when($status_filter != 'semua', function ($q) use ($status_filter) {
                return $q->where('p.status_pesanan', $status_filter);
            })
            ->orderBy('p.id_pesanan', 'desc')
            ->get();

        // Query Jastip — pakai jastip_orders (modul baru) + JOIN ke mitra
        $query_jastip = DB::table('jastip_orders as jo')
            ->leftJoin('mitra as m', 'jo.mitra_id', '=', 'm.id_mitra')
            ->select(
                'jo.id as id_jastip',
                'jo.pelanggan_id as id_pelanggan',
                'jo.mitra_id as id_mitra',
                'jo.status as status_jastip',
                'jo.ongkos_jasa as ongkir',
                'jo.actual_total_barang as total_harga_barang',
                'jo.komisi_zasha as total_admin_lokasi',
                'jo.created_at as waktu_order',
                'm.nama_panggilan as nama_driver',
                'm.foto_mitra as foto_driver'
            )
            ->where('jo.pelanggan_id', $id_pelanggan)
            ->when($status_filter != 'semua', function ($q) use ($status_filter) {
                return $q->where('jo.status', $status_filter);
            })
            ->orderByDesc('jo.id')
            ->get();

        return view('pelanggan.riwayat', [
            'query_jasa' => $query_jasa,
            'query_jastip' => $query_jastip,
            'status_filter' => $status_filter,
            'getBadgeColor' => fn($status) => $this->getBadgeColor($status)
        ]);
    }

    public function updateStatus(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id();
        $id_o = $request->id_order;
        $aksi = $request->aksi;
        $type = $request->get('type', 'jasa');

        if ($type == 'jastip') {
            // Map aksi pelanggan ke enum jastip_orders.status (modul baru)
            $status_baru = ($aksi == 'selesai') ? 'selesai' : (($aksi == 'batal') ? 'ditolak' : '');
            if (!empty($status_baru)) {
                try {
                    DB::table('jastip_orders')
                        ->where('id', $id_o)
                        ->where('pelanggan_id', $id_pelanggan)
                        ->update(['status' => $status_baru]);
                } catch (\Exception $e) {
                    Log::error('Riwayat update jastip gagal', [
                        'message'      => $e->getMessage(),
                        'id_jastip'    => $id_o,
                        'id_pelanggan' => $id_pelanggan,
                        'aksi'         => $aksi,
                    ]);
                }
            }
        } else {
            $status_baru = ($aksi == 'selesai') ? 'Selesai' : (($aksi == 'batal') ? 'Batal' : '');
            if (!empty($status_baru)) {
                DB::table('pesanan_mitra')
                    ->where('id_pesanan', $id_o)
                    ->where('id_pelanggan', $id_pelanggan)
                    ->update(['status_pesanan' => $status_baru]);

                if ($status_baru === 'Selesai') {
                    NotifHelper::kirim(
                        $id_pelanggan,
                        'Pesanan Selesai!',
                        'Pesanan #' . $id_o . ' telah selesai. Jangan lupa berikan ulasan untuk mitra.',
                        'pesanan',
                        route('pelanggan.riwayat.index')
                    );
                } elseif ($status_baru === 'Batal') {
                    NotifHelper::kirim(
                        $id_pelanggan,
                        'Pesanan Dibatalkan',
                        'Pesanan #' . $id_o . ' telah dibatalkan.',
                        'info',
                        route('pelanggan.riwayat.index')
                    );
                }
            }
        }

        return redirect()->route('pelanggan.riwayat.index');
    }

    public function kirimUlasan(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id();

        $request->validate([
            'id_order'     => 'required|integer',
            'rating_nilai' => 'required|integer|min:1|max:5',
            'ulasan_teks'  => 'nullable|string|max:500',
        ]);

        // whereNull('rating') mencegah overwrite ulasan yang sudah pernah dikirim
        DB::table('pesanan_mitra')
            ->where('id_pesanan', $request->id_order)
            ->where('id_pelanggan', $id_pelanggan)
            ->whereNull('rating')
            ->update([
                'rating' => $request->rating_nilai,
                'ulasan' => $request->ulasan_teks,
            ]);

        return redirect()->route('pelanggan.riwayat.index');
    }
}