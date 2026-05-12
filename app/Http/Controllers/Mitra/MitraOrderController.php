<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\OrderTracking;
use App\Models\MitraNotifikasi;
use App\Services\OrderTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MitraOrderController extends Controller
{
    private OrderTrackingService $orderTrackingService;

    public function __construct(OrderTrackingService $orderTrackingService)
    {
        $this->orderTrackingService = $orderTrackingService;
    }

    private function mitra()
    {
        return Auth::guard('mitra')->user();
    }

    public function getNotifikasi(): JsonResponse
    {
        $mitraId = $this->mitra()->id_mitra;

        $notifikasis = MitraNotifikasi::where('mitra_id', $mitraId)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'tipe' => $notif->tipe,
                    'judul' => $notif->judul,
                    'pesan' => $notif->pesan,
                    'tracking_id' => $notif->tracking_id,
                    'created_at' => $notif->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $notifikasis->count(),
            'data' => $notifikasis,
        ]);
    }

    public function getPendingOrder(): JsonResponse
    {
        $mitraId = $this->mitra()->id_mitra;

        $pending = OrderTracking::where('mitra_id', $mitraId)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$pending) {
            return response()->json([
                'order' => null,
            ]);
        }

        return response()->json([
            'order' => [
                'id' => $pending->id,
                'order_type' => $pending->order_type,
                'order_id' => $pending->order_id,
                'harga_jual' => $pending->harga_jual,
                'harga_modal' => $pending->harga_modal,
                'komisi_zasha' => $pending->komisi_zasha,
                'pelanggan' => [
                    'id' => $pending->pelanggan->id_pelanggan,
                    'nama' => $pending->pelanggan->nama_panggilan ?? $pending->pelanggan->nama_pelanggan ?? 'Pelanggan',
                    'no_wa' => $pending->pelanggan->no_wa ?? '-',
                ],
                'created_at' => $pending->created_at,
                'created_at_humanized' => $pending->created_at->diffForHumans(),
            ],
        ]);
    }

    public function acceptOrder(Request $request): JsonResponse
    {
        $request->validate([
            'tracking_id' => 'required|exists:order_trackings,id',
        ]);

        try {
            $tracking = OrderTracking::findOrFail($request->tracking_id);

            if ($tracking->mitra_id !== $this->mitra()->id_mitra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke order ini.',
                ], 403);
            }

            $this->orderTrackingService->mitraAccept($tracking);

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil diterima.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error('MitraOrderController::acceptOrder failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $request->tracking_id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerima order. Coba lagi.',
            ], 500);
        }
    }

    public function rejectOrder(Request $request): JsonResponse
    {
        $request->validate([
            'tracking_id' => 'required|exists:order_trackings,id',
            'pesan' => 'required|string|max:255',
        ]);

        try {
            $tracking = OrderTracking::findOrFail($request->tracking_id);

            if ($tracking->mitra_id !== $this->mitra()->id_mitra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke order ini.',
                ], 403);
            }

            $this->orderTrackingService->mitraReject($tracking, $request->pesan);

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil ditolak.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error('MitraOrderController::rejectOrder failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $request->tracking_id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak order. Coba lagi.',
            ], 500);
        }
    }

    public function updateProgress(Request $request): JsonResponse
    {
        $request->validate([
            'tracking_id' => 'required|exists:order_trackings,id',
            'status' => 'required|string|in:menuju_lokasi,di_lokasi,dikerjakan,selesai_mitra,belum_selesai',
        ]);

        try {
            $tracking = OrderTracking::findOrFail($request->tracking_id);

            if ($tracking->mitra_id !== $this->mitra()->id_mitra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke order ini.',
                ], 403);
            }

            $this->orderTrackingService->updateProgress($tracking, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'Status order berhasil diupdate.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            Log::error('MitraOrderController::updateProgress failed', [
                'message' => $e->getMessage(),
                'tracking_id' => $request->tracking_id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status. Coba lagi.',
            ], 500);
        }
    }
}
