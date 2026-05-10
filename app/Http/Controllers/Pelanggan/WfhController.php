<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\{MasterLayanan, MitraLayanan, WfhOrder};
use App\Services\WfhService;
use Illuminate\Http\Request;

class WfhController extends Controller
{
    public function __construct(private WfhService $wfhService) {}

    // Halaman form order WFH
    public function create(Request $request)
    {
        $mitraLayananId = $request->query('layanan');
        $layanan = MitraLayanan::with(['mitra', 'masterLayanan'])
            ->findOrFail($mitraLayananId);

        return view('pelanggan.wfh.create', compact('layanan'));
    }

    // Simpan order baru
    public function store(Request $request)
    {
        $request->validate([
            'mitra_layanan_id'  => 'required|exists:mitra_layanans,id',
            'brief_description' => 'required|string|min:20',
            'reference_url'     => 'nullable|url',
            'quantity'          => 'nullable|numeric|min:0.5',
            'tipe_order'        => 'required|in:reguler,express',
        ]);

        $pelanggan = auth('pelanggan')->user();

        try {
            $order = $this->wfhService->buatOrder(
                $request->only(['mitra_layanan_id', 'brief_description', 'reference_url', 'quantity', 'tipe_order']),
                $pelanggan->id_pelanggan
            );

            return redirect()->route('pelanggan.wfh.show', $order->id)
                ->with('success', 'Order berhasil dibuat! Menunggu konfirmasi mitra.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // Detail order
    public function show(WfhOrder $wfhOrder)
    {
        $this->authorizePelanggan($wfhOrder);
        $wfhOrder->load(['mitra', 'mitraLayanan.masterLayanan', 'escrowLedgers']);
        return view('pelanggan.wfh.show', ['order' => $wfhOrder]);
    }

    // Konfirmasi pekerjaan selesai
    public function konfirmasi(WfhOrder $wfhOrder)
    {
        $this->authorizePelanggan($wfhOrder);

        try {
            $this->wfhService->konfirmasiSelesai($wfhOrder, auth('pelanggan')->id());
            return redirect()->route('pelanggan.wfh.show', $wfhOrder->id)
                ->with('success', 'Terima kasih! Pekerjaan telah selesai dan pembayaran diteruskan ke mitra.');
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Buka dispute
    public function dispute(Request $request, WfhOrder $wfhOrder)
    {
        $this->authorizePelanggan($wfhOrder);
        $request->validate(['deskripsi' => 'required|string|min:20']);

        try {
            $this->wfhService->bukaDispute($wfhOrder, auth('pelanggan')->id(), $request->deskripsi);
            return redirect()->route('pelanggan.wfh.show', $wfhOrder->id)
                ->with('warning', 'Dispute berhasil dibuka. Admin akan menghubungi Anda segera.');
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Daftar semua order WFH pelanggan
    public function index()
    {
        $orders = WfhOrder::where('pelanggan_id', auth('pelanggan')->id())
            ->latest()->paginate(10);
        return view('pelanggan.wfh.index', compact('orders'));
    }

    private function authorizePelanggan(WfhOrder $order): void
    {
        if ((int) $order->pelanggan_id !== auth('pelanggan')->id()) {
            abort(403);
        }
    }
}
