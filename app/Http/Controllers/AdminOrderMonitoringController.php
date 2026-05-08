<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $filter_status = $request->query('status', '');
        $search = $request->query('search', '');

        // QUERY UNION SAKTI (TIDAK DIUBAH)
        $query_text = "
            (SELECT 
                CAST(p.id_pesanan AS CHAR) COLLATE utf8mb4_general_ci as id, 
                p.tanggal_pesanan as tgl, 
                p.status_pesanan COLLATE utf8mb4_general_ci as status, 
                p.metode_pembayaran COLLATE utf8mb4_general_ci as metode,
                'JASA' COLLATE utf8mb4_general_ci as tipe_order,
                plg.nama_pelanggan COLLATE utf8mb4_general_ci as nama_pelanggan, 
                COALESCE(m.nama_mitra, '-') COLLATE utf8mb4_general_ci as nama_pekerja,
                p.kategori_jasa COLLATE utf8mb4_general_ci as detail,
                (p.biaya_jasa + p.ongkir + p.kode_unik) as total_biaya
            FROM pesanan p
            JOIN pelanggan plg ON p.id_pelanggan = plg.id_pelanggan
            LEFT JOIN mitra m ON p.id_mitra = m.id_mitra
            WHERE p.status_pesanan NOT IN ('Selesai', 'Batal'))
            
            UNION ALL
            
            (SELECT 
                CAST(pj.id_jastip AS CHAR) COLLATE utf8mb4_general_ci as id, 
                pj.waktu_order as tgl, 
                pj.status_jastip COLLATE utf8mb4_general_ci as status, 
                pj.metode_pembayaran COLLATE utf8mb4_general_ci as metode,
                'JASTIP' COLLATE utf8mb4_general_ci as tipe_order,
                plg.nama_pelanggan COLLATE utf8mb4_general_ci as nama_pelanggan, 
                COALESCE(mj.nama_mitra, '-') COLLATE utf8mb4_general_ci as nama_pekerja,
                pj.lokasi_asal COLLATE utf8mb4_general_ci as detail,
                (pj.total_harga_barang + pj.ongkir) as total_biaya
            FROM pesanan_jastip pj
            JOIN pelanggan plg ON pj.id_pelanggan = plg.id_pelanggan
            LEFT JOIN mitra mj ON pj.id_mitra = mj.id_mitra
            WHERE pj.status_jastip NOT IN ('Selesai', 'Batal'))
        ";

        $final_query = "SELECT * FROM ($query_text) as gabungan WHERE 1=1";
        
        if ($filter_status != '') {
            $final_query .= " AND status = '$filter_status'";
        }
        if ($search != '') {
            $final_query .= " AND (id LIKE '%$search%' OR nama_pelanggan LIKE '%$search%' OR nama_pekerja LIKE '%$search%')";
        }

        $final_query .= " ORDER BY FIELD(status, 'Menunggu Konfirmasi', 'Proses', 'Pending', 'Lunas'), tgl DESC";

        $orders = DB::select($final_query);

        return view('admin.orders.index', compact('orders', 'filter_status', 'search'));
    }

    public function updateStatus(Request $request)
    {
        $table = ($request->tipe_order == 'JASA') ? 'pesanan' : 'pesanan_jastip';
        $id_column = ($request->tipe_order == 'JASA') ? 'id_pesanan' : 'id_jastip';
        $status_column = ($request->tipe_order == 'JASA') ? 'status_pesanan' : 'status_jastip';

        DB::table($table)->where($id_column, $request->id_pesanan)->update([
            $status_column => $request->status_baru
        ]);

        return redirect()->back()->with('pesan', 'Status berhasil diperbarui!');
    }
}
