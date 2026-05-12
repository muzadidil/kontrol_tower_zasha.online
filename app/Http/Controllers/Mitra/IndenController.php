<?php

namespace App\Http\Controllers\Mitra;

use App\Enums\IndenOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\IndenOrder;
use App\Services\IndenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IndenController extends Controller
{
    public function __construct(private IndenService $indenService) {}

    /**
     * Daftar order inden mitra. Filter: ?status=menunggu_mitra|aktif|riwayat
     */
    public function index(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();
        $filter = $request->query('status', 'aktif');

        $query = IndenOrder::where('mitra_id', $mitra->id_mitra)
            ->with('pelanggan:id_pelanggan,nama_pelanggan,nama_panggilan,no_wa')
            ->orderByDesc('tanggal_pelaksanaan');

        if ($filter === 'menunggu_mitra') {
            $query->where('status', IndenOrderStatus::MenungguMitra);
        } elseif ($filter === 'aktif') {
            $query->whereIn('status', [
                IndenOrderStatus::MenungguDp,
                IndenOrderStatus::DpDibayar,
                IndenOrderStatus::Dikerjakan,
                IndenOrderStatus::MenungguPelunasan,
            ]);
        } elseif ($filter === 'riwayat') {
            $query->whereIn('status', [
                IndenOrderStatus::Selesai,
                IndenOrderStatus::Ditolak,
                IndenOrderStatus::Dispute,
            ]);
        }

        $orders = $query->get();

        // Counter per kategori (untuk badge filter)
        $counts = [
            'menunggu_mitra' => IndenOrder::where('mitra_id', $mitra->id_mitra)
                ->where('status', IndenOrderStatus::MenungguMitra)->count(),
            'aktif' => IndenOrder::where('mitra_id', $mitra->id_mitra)
                ->whereIn('status', [
                    IndenOrderStatus::MenungguDp,
                    IndenOrderStatus::DpDibayar,
                    IndenOrderStatus::Dikerjakan,
                    IndenOrderStatus::MenungguPelunasan,
                ])->count(),
        ];

        return view('mitra.inden.index', compact('mitra', 'orders', 'filter', 'counts'));
    }

    /**
     * Detail order inden.
     */
    public function show(IndenOrder $indenOrder)
    {
        $this->authorize_($indenOrder);
        $indenOrder->load('pelanggan');
        return view('mitra.inden.show', ['order' => $indenOrder]);
    }

    /**
     * Setujui order inden.
     */
    public function approve(IndenOrder $indenOrder)
    {
        $this->authorize_($indenOrder);
        $mitra = Auth::guard('mitra')->user();

        try {
            $this->indenService->approveOrder($indenOrder, $mitra->id_mitra);
            return redirect()->route('mitra.inden.show', $indenOrder)
                ->with('success', 'Order inden disetujui. Pelanggan akan diminta bayar DP.');
        } catch (\Exception $e) {
            Log::error('Mitra inden approve failed', [
                'order_id' => $indenOrder->id,
                'mitra_id' => $mitra->id_mitra,
                'error'    => $e->getMessage(),
            ]);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tolak order inden dengan alasan.
     */
    public function tolak(Request $request, IndenOrder $indenOrder)
    {
        $this->authorize_($indenOrder);
        $mitra = Auth::guard('mitra')->user();

        $data = $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        try {
            $this->indenService->tolakOrder($indenOrder, $mitra->id_mitra, $data['alasan']);
            return redirect()->route('mitra.inden.index')
                ->with('success', 'Order ditolak. Pelanggan akan diberi tahu.');
        } catch (\Exception $e) {
            Log::error('Mitra inden tolak failed', [
                'order_id' => $indenOrder->id,
                'mitra_id' => $mitra->id_mitra,
                'error'    => $e->getMessage(),
            ]);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mitra tiba di lokasi (status -> dikerjakan).
     */
    public function mulaiKerja(IndenOrder $indenOrder)
    {
        $this->authorize_($indenOrder);
        $mitra = Auth::guard('mitra')->user();

        try {
            $this->indenService->mitraTiba($indenOrder, $mitra->id_mitra);
            return redirect()->route('mitra.inden.show', $indenOrder)
                ->with('success', 'Status order diubah ke "Sedang Dikerjakan".');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mitra selesai kerja -> status menunggu_pelunasan.
     */
    public function selesaiKerja(IndenOrder $indenOrder)
    {
        $this->authorize_($indenOrder);

        if ($indenOrder->status !== IndenOrderStatus::Dikerjakan) {
            return back()->with('error', 'Order belum dalam status "Dikerjakan".');
        }

        try {
            $indenOrder->update(['status' => IndenOrderStatus::MenungguPelunasan]);
            return redirect()->route('mitra.inden.show', $indenOrder)
                ->with('success', 'Order selesai. Menunggu pelanggan bayar pelunasan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Pastikan order ini milik mitra yang login.
     */
    private function authorize_(IndenOrder $order): void
    {
        $mitraId = Auth::guard('mitra')->user()->id_mitra;
        if ((int) $order->mitra_id !== (int) $mitraId) {
            abort(403, 'Anda tidak punya akses ke order ini.');
        }
    }
}
