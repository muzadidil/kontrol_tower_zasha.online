<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\OrderTracking;
use App\Services\OrderTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PelangganOrderTrackingController extends Controller
{
    private OrderTrackingService $orderTrackingService;

    public function __construct(OrderTrackingService $orderTrackingService)
    {
        $this->orderTrackingService = $orderTrackingService;
    }

    public function trackOrder(OrderTracking $tracking)
    {
        $pelangganId = Auth::guard('pelanggan')->id();

        if ($tracking->pelanggan_id !== $pelangganId) {
            return abort(403, 'Anda tidak memiliki akses ke order ini.');
        }

        $statusLabels = [
            'pending' => 'Menunggu Mitra Menerima',
            'accepted' => 'Mitra Menerima',
            'menuju_lokasi' => 'Mitra Menuju Lokasi',
            'di_lokasi' => 'Mitra Tiba di Lokasi',
            'dikerjakan' => 'Sedang Dikerjakan',
            'selesai_mitra' => 'Selesai (Menunggu Konfirmasi)',
            'belum_selesai' => 'Belum Selesai (Perbaikan)',
            'selesai' => 'Selesai & Dibayar',
            'ditolak_mitra' => 'Ditolak Mitra',
            'dibatalkan' => 'Dibatalkan',
        ];

        $tracking->status_label = $statusLabels[$tracking->status] ?? $tracking->status;

        return view('pelanggan.order-tracking', compact('tracking'));
    }

    public function getStatus(OrderTracking $tracking)
    {
        $pelangganId = Auth::guard('pelanggan')->id();

        if ($tracking->pelanggan_id !== $pelangganId) {
            return abort(403, 'Anda tidak memiliki akses ke order ini.');
        }

        return response()->json([
            'success' => true,
            'status' => $tracking->status,
            'mitra_nama' => $tracking->mitra->nama_panggilan ?? $tracking->mitra->nama_asli,
            'updated_at' => $tracking->updated_at->diffForHumans(),
        ]);
    }

    public function confirmSelesai(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|exists:order_trackings,id',
        ]);

        try {
            $tracking = OrderTracking::findOrFail($request->tracking_id);
            $pelangganId = Auth::guard('pelanggan')->id();

            if ($tracking->pelanggan_id !== $pelangganId) {
                return back()->with('error', 'Anda tidak memiliki akses ke order ini.');
            }

            $this->orderTrackingService->pelangganConfirmSelesai($tracking);

            return redirect()->route('pelanggan.riwayat.index')->with('success', 'Pekerjaan selesai! Terima kasih telah menggunakan Zasha.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('PelangganOrderTrackingController::confirmSelesai failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $request->tracking_id,
                'pelanggan_id' => Auth::guard('pelanggan')->id(),
            ]);
            return back()->with('error', 'Gagal mengonfirmasi order. Coba lagi.');
        }
    }

    public function belumSelesai(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|exists:order_trackings,id',
        ]);

        try {
            $tracking = OrderTracking::findOrFail($request->tracking_id);
            $pelangganId = Auth::guard('pelanggan')->id();

            if ($tracking->pelanggan_id !== $pelangganId) {
                return back()->with('error', 'Anda tidak memiliki akses ke order ini.');
            }

            $this->orderTrackingService->pelangganBelumSelesai($tracking);

            return back()->with('success', 'Mitra diberitahu untuk melanjutkan perbaikan.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('PelangganOrderTrackingController::belumSelesai failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $request->tracking_id,
                'pelanggan_id' => Auth::guard('pelanggan')->id(),
            ]);
            return back()->with('error', 'Gagal update status. Coba lagi.');
        }
    }
}
