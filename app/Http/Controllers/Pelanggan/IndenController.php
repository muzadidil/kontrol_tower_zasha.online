<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\{Mitra, IndenOrder};
use App\Services\IndenService;
use Illuminate\Http\Request;

class IndenController extends Controller
{
    public function __construct(private IndenService $indenService) {}

    public function store(Request $request)
    {
        $request->validate([
            'mitra_id'              => 'required|string|exists:mitras,id_mitra',
            'tanggal_pelaksanaan'   => 'required|date|after:today',
            'tipe_durasi'           => 'required|in:jam,hari',
            'durasi'                => 'required|numeric|min:0.5',
            'tarif_per_jam'         => 'required_if:tipe_durasi,jam|numeric|min:0',
            'tarif_per_hari'        => 'required_if:tipe_durasi,hari|numeric|min:0',
            'metode_pembayaran'     => 'required|in:saldo,transfer,cod',
            'alamat_pelanggan'      => 'required|string|max:255',
            'pelanggan_lat'         => 'nullable|numeric',
            'pelanggan_lng'         => 'nullable|numeric',
            'keterangan_kerja'      => 'nullable|string|max:1000',
        ]);

        try {
            $mitra = Mitra::where('id_mitra', $request->mitra_id)->firstOrFail();

            if ($mitra->status_online !== 'online') {
                return back()->with('error', 'Mitra tidak sedang online.')->withInput();
            }

            $order = $this->indenService->buatOrder($request->all(), auth('pelanggan')->id());
            return redirect()->route('pelanggan.inden.show', $order->id)->with('success', 'Order inden dibuat. Menunggu approval mitra...');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(IndenOrder $indenOrder)
    {
        $this->authorizePelanggan($indenOrder);
        $indenOrder->load(['pelanggan', 'mitra']);
        return view('pelanggan.inden.show', ['order' => $indenOrder]);
    }

    public function bayarDp(IndenOrder $indenOrder)
    {
        $this->authorizePelanggan($indenOrder);
        try {
            $this->indenService->bayarDp($indenOrder, auth('pelanggan')->id());
            return back()->with('success', 'DP berhasil dibayar.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function bayarPelunasan(IndenOrder $indenOrder)
    {
        $this->authorizePelanggan($indenOrder);
        try {
            $this->indenService->bayarPelunasan($indenOrder, auth('pelanggan')->id());
            return back()->with('success', 'Pelunasan berhasil dibayar.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function konfirmasi(IndenOrder $indenOrder)
    {
        $this->authorizePelanggan($indenOrder);
        try {
            $this->indenService->konfirmasiSelesai($indenOrder, auth('pelanggan')->id());
            return back()->with('success', 'Order selesai, terima kasih!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function dispute(Request $request, IndenOrder $indenOrder)
    {
        $this->authorizePelanggan($indenOrder);
        $request->validate(['deskripsi' => 'required|min:20']);
        try {
            $this->indenService->bukaDispute($indenOrder, auth('pelanggan')->id(), $request->deskripsi);
            return back()->with('warning', 'Dispute dibuka.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizePelanggan(IndenOrder $order): void
    {
        if ((int) $order->pelanggan_id !== auth('pelanggan')->id()) {
            abort(403);
        }
    }
}
