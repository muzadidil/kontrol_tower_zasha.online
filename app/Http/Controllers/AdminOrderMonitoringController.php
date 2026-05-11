<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminOrderMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $filter_status = $request->query('status', '');
        $search = $request->query('search', '');

        // UNION jasa (pesanan) + jastip (jastip_orders modul baru).
        // Kolom yang tidak ada di skema baru di-cast jadi placeholder agar shape kolom sama.
        $query_text = "
            (SELECT
                CAST(p.id_pesanan AS CHAR) COLLATE utf8mb4_general_ci as id,
                p.tanggal_pesanan as tgl,
                p.status_pesanan COLLATE utf8mb4_general_ci as status,
                p.metode_pembayaran COLLATE utf8mb4_general_ci as metode,
                'JASA' COLLATE utf8mb4_general_ci as tipe_order,
                plg.nama_pelanggan COLLATE utf8mb4_general_ci as nama_pelanggan,
                COALESCE(m.nama_asli, '-') COLLATE utf8mb4_general_ci as nama_pekerja,
                p.kategori_jasa COLLATE utf8mb4_general_ci as detail,
                (p.biaya_jasa + p.ongkir + p.kode_unik) as total_biaya
            FROM pesanan p
            JOIN pelanggan plg ON p.id_pelanggan = plg.id_pelanggan
            LEFT JOIN mitra m ON p.id_mitra = m.id_mitra
            WHERE p.status_pesanan NOT IN ('Selesai', 'Batal'))

            UNION ALL

            (SELECT
                CAST(jo.id AS CHAR) COLLATE utf8mb4_general_ci as id,
                jo.created_at as tgl,
                jo.status COLLATE utf8mb4_general_ci as status,
                IF(jo.cod_eligible = 1, 'COD', 'Saldo') COLLATE utf8mb4_general_ci as metode,
                'JASTIP' COLLATE utf8mb4_general_ci as tipe_order,
                plg.nama_pelanggan COLLATE utf8mb4_general_ci as nama_pelanggan,
                COALESCE(m.nama_asli, '-') COLLATE utf8mb4_general_ci as nama_pekerja,
                jo.delivery_address COLLATE utf8mb4_general_ci as detail,
                (jo.actual_total_barang + jo.ongkos_jasa) as total_biaya
            FROM jastip_orders jo
            JOIN pelanggan plg ON jo.pelanggan_id = plg.id_pelanggan
            LEFT JOIN mitra m ON jo.mitra_id = m.id_mitra
            WHERE jo.status NOT IN ('selesai', 'ditolak'))
        ";

        $final_query = "SELECT * FROM ($query_text) as gabungan WHERE 1=1";
        $bindings = [];

        if ($filter_status != '') {
            $final_query .= " AND status = ?";
            $bindings[] = $filter_status;
        }
        if ($search != '') {
            $final_query .= " AND (id LIKE ? OR nama_pelanggan LIKE ? OR nama_pekerja LIKE ?)";
            $like = '%' . $search . '%';
            $bindings[] = $like;
            $bindings[] = $like;
            $bindings[] = $like;
        }

        $final_query .= " ORDER BY FIELD(status, 'Menunggu Konfirmasi', 'Proses', 'Pending', 'Lunas', 'menunggu_mitra', 'belanja', 'menuju_pengantaran', 'diantar'), tgl DESC";

        $orders = DB::select($final_query, $bindings);

        return view('admin.orders.index', compact('orders', 'filter_status', 'search'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'tipe_order'  => 'required|in:JASA,JASTIP',
            'id_pesanan'  => 'required',
            'status_baru' => 'required|string|max:50',
        ]);

        try {
            if ($request->tipe_order == 'JASA') {
                DB::table('pesanan')
                    ->where('id_pesanan', $request->id_pesanan)
                    ->update(['status_pesanan' => $request->status_baru]);
            } else {
                DB::table('jastip_orders')
                    ->where('id', $request->id_pesanan)
                    ->update(['status' => $request->status_baru]);
            }

            return redirect()->back()->with('pesan', 'Status berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Admin update order status error', [
                'message'     => $e->getMessage(),
                'tipe_order'  => $request->tipe_order,
                'id_pesanan'  => $request->id_pesanan,
                'status_baru' => $request->status_baru,
            ]);
            return back()->with('pesan', 'Gagal update status.');
        }
    }
}
