<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{TopupRequest, WalletTransfer, WithdrawalRequest};
use App\Services\TokopayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function __construct(private TokopayService $tokopay) {}

    public function saldo(Request $request)
    {
        return response()->json([
            'saldo' => $request->user()->saldo,
        ]);
    }

    public function topup(Request $request)
    {
        $request->validate([
            'jumlah'    => 'required|numeric|min:10000',
            'user_type' => 'required|in:pelanggan,mitra',
        ]);

        $userId = $request->user_type === 'pelanggan'
            ? $request->user()->id_pelanggan
            : $request->user()->id_mitra;

        $result = $this->tokopay->createTopup($request->user_type, $userId, $request->jumlah);

        return response()->json([
            'topup_id' => $result['topup']->id,
            'pay_url'  => $result['pay_url'],
            'qr_url'   => $result['qr_url'],
        ]);
    }

    public function riwayatTopup(Request $request)
    {
        $userType = method_exists($request->user(), 'id_pelanggan') && $request->user()->id_pelanggan
            ? 'pelanggan' : 'mitra';
        $userId = $userType === 'pelanggan' ? $request->user()->id_pelanggan : $request->user()->id_mitra;

        $topups = TopupRequest::where('user_type', $userType)
            ->where('user_id', $userId)
            ->latest()->paginate(20);

        return response()->json($topups);
    }

    // Mitra → Mitra transfer
    public function transferMitra(Request $request)
    {
        $request->validate([
            'ke_mitra_id' => 'required|exists:mitra,id_mitra',
            'jumlah'      => 'required|numeric|min:1000',
            'catatan'     => 'nullable|string',
        ]);

        $dariMitraId = $request->user()->id_mitra;

        if ($dariMitraId == $request->ke_mitra_id) {
            return response()->json(['message' => 'Tidak bisa transfer ke diri sendiri.'], 422);
        }

        return DB::transaction(function () use ($request, $dariMitraId) {
            $dari = \App\Models\Mitra::where('id_mitra', $dariMitraId)->lockForUpdate()->firstOrFail();
            if ($dari->saldo < $request->jumlah) {
                return response()->json(['message' => 'Saldo tidak cukup.'], 422);
            }
            $ke = \App\Models\Mitra::where('id_mitra', $request->ke_mitra_id)->lockForUpdate()->firstOrFail();

            $dari->decrement('saldo', $request->jumlah);
            $ke->increment('saldo', $request->jumlah);

            WalletTransfer::create([
                'dari_mitra_id' => $dariMitraId,
                'ke_mitra_id'   => $request->ke_mitra_id,
                'jumlah'        => $request->jumlah,
                'catatan'       => $request->catatan,
                'status'        => 'berhasil',
            ]);

            return response()->json(['message' => 'Transfer berhasil.']);
        });
    }

    // Withdrawal mitra
    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'jumlah'         => 'required|numeric|min:50000',
            'nama_rekening'  => 'required|string',
            'nomor_rekening' => 'required|string',
            'nama_bank'      => 'required|string',
        ]);

        $mitra = $request->user();
        if ($mitra->saldo < $request->jumlah) {
            return response()->json(['message' => 'Saldo tidak cukup.'], 422);
        }

        $wd = WithdrawalRequest::create([
            'mitra_id'       => $mitra->id_mitra,
            'jumlah'         => $request->jumlah,
            'nama_rekening'  => $request->nama_rekening,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_bank'      => $request->nama_bank,
            'status'         => 'pending',
        ]);

        return response()->json(['withdrawal' => $wd], 201);
    }
}
