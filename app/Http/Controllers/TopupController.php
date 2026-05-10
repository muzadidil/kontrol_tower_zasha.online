<?php

namespace App\Http\Controllers;

use App\Models\TopupRequest;
use App\Services\TokopayService;
use Illuminate\Http\Request;

class TopupController extends Controller
{
    public function __construct(private TokopayService $tokopay) {}

    // Form topup pelanggan
    public function pelangganForm()
    {
        return view('pelanggan.topup.form');
    }

    public function pelangganStore(Request $request)
    {
        $request->validate(['jumlah' => 'required|numeric|min:10000']);

        try {
            $result = $this->tokopay->createTopup('pelanggan', auth('pelanggan')->id(), $request->jumlah);
            if (! $result['pay_url']) {
                return back()->with('error', 'Gagal membuat link pembayaran. Coba lagi.');
            }
            return redirect()->away($result['pay_url']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Form topup mitra
    public function mitraForm()
    {
        return view('mitra.topup.form');
    }

    public function mitraStore(Request $request)
    {
        $request->validate(['jumlah' => 'required|numeric|min:10000']);

        try {
            $result = $this->tokopay->createTopup('mitra', auth('mitra')->user()->id_mitra, $request->jumlah);
            if (! $result['pay_url']) {
                return back()->with('error', 'Gagal membuat link pembayaran.');
            }
            return redirect()->away($result['pay_url']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Webhook Tokopay
    public function webhook(Request $request)
    {
        try {
            $this->tokopay->handleWebhook($request->all());
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            \Log::error('Tokopay webhook error', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    // Riwayat topup pelanggan
    public function pelangganIndex()
    {
        $topups = TopupRequest::where('user_type', 'pelanggan')
            ->where('user_id', auth('pelanggan')->id())
            ->latest()->paginate(20);
        return view('pelanggan.topup.index', compact('topups'));
    }

    public function mitraIndex()
    {
        $topups = TopupRequest::where('user_type', 'mitra')
            ->where('user_id', auth('mitra')->user()->id_mitra)
            ->latest()->paginate(20);
        return view('mitra.topup.index', compact('topups'));
    }
}
