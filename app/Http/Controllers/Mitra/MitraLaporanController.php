<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraLaporanController extends Controller
{
    /**
     * Laporan penghasilan harian (per tanggal yang dipilih, default hari ini).
     */
    public function harian(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();

        $tanggal = $request->query('tanggal')
            ? Carbon::parse($request->query('tanggal'))->startOfDay()
            : Carbon::today();

        $orders = DB::table('order_trackings as ot')
            ->leftJoin('pelanggans as p', 'ot.pelanggan_id', '=', 'p.id_pelanggan')
            ->select(
                'ot.id', 'ot.order_type', 'ot.status', 'ot.harga_jual',
                'ot.komisi_zasha', 'ot.created_at', 'ot.updated_at',
                'p.nama_pelanggan', 'p.nama_panggilan as pelanggan_panggilan'
            )
            ->where('ot.mitra_id', $mitra->id_mitra)
            ->where('ot.status', 'selesai')
            ->whereDate('ot.updated_at', $tanggal)
            ->orderByDesc('ot.updated_at')
            ->get();

        $stats = [
            'total_order'    => $orders->count(),
            'total_omset'    => $orders->sum('harga_jual'),
            'total_komisi'   => $orders->sum('komisi_zasha'),
            'total_bersih'   => $orders->sum('harga_jual') - $orders->sum('komisi_zasha'),
        ];

        // Sliding range 7 hari terakhir untuk nav
        $rangeHari = collect(range(0, 6))->map(fn($i) => Carbon::today()->subDays($i))->reverse()->values();

        return view('mitra.laporan.harian', compact('mitra', 'orders', 'stats', 'tanggal', 'rangeHari'));
    }

    /**
     * Laporan penghasilan bulanan (per bulan, default bulan ini).
     * Breakdown per hari + breakdown per jenis order.
     */
    public function bulanan(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();

        $bulan = $request->query('bulan')
            ? Carbon::parse($request->query('bulan') . '-01')->startOfMonth()
            : Carbon::now()->startOfMonth();
        $akhirBulan = $bulan->copy()->endOfMonth();

        $orders = DB::table('order_trackings')
            ->select(
                'id', 'order_type', 'harga_jual', 'komisi_zasha', 'updated_at',
                DB::raw('DATE(updated_at) as tanggal')
            )
            ->where('mitra_id', $mitra->id_mitra)
            ->where('status', 'selesai')
            ->whereBetween('updated_at', [$bulan, $akhirBulan])
            ->orderByDesc('updated_at')
            ->get();

        $stats = [
            'total_order'    => $orders->count(),
            'total_omset'    => $orders->sum('harga_jual'),
            'total_komisi'   => $orders->sum('komisi_zasha'),
            'total_bersih'   => $orders->sum('harga_jual') - $orders->sum('komisi_zasha'),
            'rata_per_order' => $orders->isNotEmpty() ? $orders->avg('harga_jual') : 0,
        ];

        // Breakdown per hari (untuk chart sederhana)
        $perHari = $orders->groupBy('tanggal')->map(function ($items, $tgl) {
            return (object) [
                'tanggal' => Carbon::parse($tgl),
                'jumlah'  => $items->count(),
                'omset'   => $items->sum('harga_jual'),
                'bersih'  => $items->sum('harga_jual') - $items->sum('komisi_zasha'),
            ];
        })->sortByDesc(fn($r) => $r->tanggal->timestamp)->values();

        // Breakdown per jenis order
        $perJenis = $orders->groupBy('order_type')->map(function ($items, $type) {
            return (object) [
                'order_type' => $type,
                'jumlah'     => $items->count(),
                'omset'      => $items->sum('harga_jual'),
                'bersih'     => $items->sum('harga_jual') - $items->sum('komisi_zasha'),
            ];
        })->sortByDesc('omset')->values();

        // Sliding range 6 bulan terakhir untuk nav
        $rangeBulan = collect(range(0, 5))->map(fn($i) => Carbon::now()->startOfMonth()->subMonths($i))->reverse()->values();

        return view('mitra.laporan.bulanan', compact(
            'mitra', 'stats', 'bulan', 'perHari', 'perJenis', 'rangeBulan'
        ));
    }
}
