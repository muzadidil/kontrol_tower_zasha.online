<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraMapsController extends Controller
{
    /**
     * List semua order aktif mitra dengan lokasi pelanggan untuk navigasi.
     * Order ditampilkan grouped by status agar yang urgent (menuju_lokasi) di atas.
     */
    public function index()
    {
        $mitra = Auth::guard('mitra')->user();

        $activeStatuses = ['accepted', 'menuju_lokasi', 'di_lokasi', 'dikerjakan', 'belum_selesai'];

        // Ambil semua tracking aktif mitra
        $trackings = DB::table('order_trackings')
            ->where('mitra_id', $mitra->id_mitra)
            ->whereIn('status', $activeStatuses)
            ->orderByRaw("FIELD(status, 'menuju_lokasi','di_lokasi','dikerjakan','accepted','belum_selesai')")
            ->orderByDesc('updated_at')
            ->get();

        // Enrich dengan lokasi & data pelanggan per order_type
        $orders = $trackings->map(function ($t) {
            $info = $this->resolveLocation($t->order_type, $t->order_id);
            if (!$info) return null;

            $pelanggan = DB::table('pelanggans')
                ->where('id_pelanggan', $t->pelanggan_id)
                ->select('id_pelanggan', 'nama_pelanggan', 'nama_panggilan', 'no_wa')
                ->first();

            return (object) [
                'tracking_id' => $t->id,
                'order_type'  => $t->order_type,
                'status'      => $t->status,
                'updated_at'  => $t->updated_at,
                'lat'         => $info['lat'],
                'lng'         => $info['lng'],
                'alamat'      => $info['alamat'],
                'multi_stop'  => $info['multi_stop'] ?? false,
                'stops_count' => $info['stops_count'] ?? 0,
                'pelanggan'   => $pelanggan,
            ];
        })->filter()->values();

        return view('mitra.maps.index', compact('mitra', 'orders'));
    }

    /**
     * Resolve lokasi pelanggan per jenis order.
     * Return ['lat' => ..., 'lng' => ..., 'alamat' => ..., 'multi_stop' => bool] atau null.
     */
    private function resolveLocation(string $orderType, int $orderId): ?array
    {
        switch ($orderType) {
            case 'tenaga':
                $row = DB::table('tenaga_orders')
                    ->where('id', $orderId)
                    ->select('pelanggan_lat as lat', 'pelanggan_lng as lng', 'alamat_pelanggan as alamat')
                    ->first();
                return $row && $row->lat ? (array) $row : null;

            case 'service':
                $row = DB::table('service_orders')
                    ->where('id', $orderId)
                    ->select('pelanggan_lat as lat', 'pelanggan_lng as lng', 'alamat_pelanggan as alamat')
                    ->first();
                return $row && $row->lat ? (array) $row : null;

            case 'inden':
                $row = DB::table('inden_orders')
                    ->where('id', $orderId)
                    ->select('pelanggan_lat as lat', 'pelanggan_lng as lng', 'alamat_pelanggan as alamat')
                    ->first();
                return $row && $row->lat ? (array) $row : null;

            case 'jastip':
                $row = DB::table('jastip_orders')
                    ->where('id', $orderId)
                    ->select('delivery_lat as lat', 'delivery_lng as lng', 'delivery_address as alamat')
                    ->first();
                if (!$row || !$row->lat) return null;

                $stopsCount = DB::table('jastip_stops')->where('jastip_order_id', $orderId)->count();
                return [
                    'lat'         => $row->lat,
                    'lng'         => $row->lng,
                    'alamat'      => $row->alamat,
                    'multi_stop'  => $stopsCount > 0,
                    'stops_count' => $stopsCount,
                ];

            default:
                return null;
        }
    }
}
