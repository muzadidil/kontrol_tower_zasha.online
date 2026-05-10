<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\{JastipOrder, Mitra};
use App\Services\JastipService;
use Illuminate\Http\Request;

class JastipController extends Controller
{
    public function __construct(private JastipService $jastipService) {}

    // Form buat order Jastip
    public function create()
    {
        $mitraJastip = Mitra::where('kategori_kode', 'JST')
            ->where('status_verifikasi', 'active')
            ->get();

        return view('pelanggan.jastip.create', compact('mitraJastip'));
    }

    // Simpan order baru
    public function store(Request $request)
    {
        $request->validate([
            'mitra_id'         => 'nullable',
            'delivery_address' => 'required|string',
            'delivery_lat'     => 'required|numeric',
            'delivery_lng'     => 'required|numeric',
            'stops'            => 'required|array|min:1',
            'stops.*.nama_lokasi'   => 'required|string',
            'stops.*.alamat_lokasi' => 'required|string',
            'stops.*.lat'           => 'required|numeric',
            'stops.*.lng'           => 'required|numeric',
            'stops.*.items'         => 'required|array|min:1',
            'stops.*.items.*.nama_barang'     => 'required|string',
            'stops.*.items.*.harga_perkiraan' => 'required|numeric|min:0',
        ]);

        try {
            $order = $this->jastipService->buatOrder(
                $request->only(['mitra_id', 'delivery_address', 'delivery_lat', 'delivery_lng', 'stops']),
                auth('pelanggan')->id()
            );

            return redirect()->route('pelanggan.jastip.show', $order->id)
                ->with('success', 'Order Jastip berhasil dibuat. Menunggu mitra konfirmasi.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // Daftar order
    public function index()
    {
        $orders = JastipOrder::where('pelanggan_id', auth('pelanggan')->id())
            ->latest()->paginate(10);

        return view('pelanggan.jastip.index', compact('orders'));
    }

    // Detail order
    public function show(JastipOrder $jastipOrder)
    {
        $this->authorizePelanggan($jastipOrder);
        $jastipOrder->load(['mitra', 'stops.items']);

        return view('pelanggan.jastip.show', ['order' => $jastipOrder]);
    }

    // Konfirmasi terima
    public function konfirmasi(JastipOrder $jastipOrder)
    {
        $this->authorizePelanggan($jastipOrder);

        try {
            $this->jastipService->konfirmasiTerima($jastipOrder, auth('pelanggan')->id());
            return back()->with('success', 'Terima kasih! Order selesai dan komisi diteruskan ke Zasha.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Buka dispute
    public function dispute(Request $request, JastipOrder $jastipOrder)
    {
        $this->authorizePelanggan($jastipOrder);
        $request->validate(['deskripsi' => 'required|string|min:20']);

        try {
            $this->jastipService->bukaDispute($jastipOrder, auth('pelanggan')->id(), $request->deskripsi);
            return back()->with('warning', 'Dispute terbuka. Admin akan mediasi segera.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizePelanggan(JastipOrder $order): void
    {
        if ((int) $order->pelanggan_id !== auth('pelanggan')->id()) abort(403);
    }
}
