<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PpobTransaction;
use App\Services\PpobService;
use Illuminate\Http\Request;

class PpobApiController extends Controller
{
    public function __construct(private PpobService $ppob) {}

    public function index(Request $request)
    {
        $userType = $this->getUserType($request);
        $userId   = $userType === 'pelanggan' ? $request->user()->id_pelanggan : $request->user()->id_mitra;

        $trxs = PpobTransaction::where('user_type', $userType)
            ->where('user_id', $userId)
            ->latest()->paginate(20);

        return response()->json($trxs);
    }

    public function transaksi(Request $request)
    {
        $request->validate([
            'jenis_produk' => 'required|in:pulsa,paket_data,token_listrik',
            'nomor_tujuan' => 'required|string',
            'kode_produk'  => 'required|string',
            'nama_produk'  => 'required|string',
            'harga_modal'  => 'required|numeric',
            'harga_jual'   => 'required|numeric',
        ]);

        $userType = $this->getUserType($request);
        $userId   = $userType === 'pelanggan' ? $request->user()->id_pelanggan : $request->user()->id_mitra;

        try {
            $trx = $this->ppob->createTransaction(
                $request->only(['jenis_produk', 'nomor_tujuan', 'kode_produk', 'nama_produk', 'harga_modal', 'harga_jual']),
                $userType,
                $userId
            );
            $this->ppob->callDigiflazz($trx);
            return response()->json(['transaksi' => $trx->fresh()], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    private function getUserType(Request $request): string
    {
        return $request->user() instanceof \App\Models\Mitra ? 'mitra' : 'pelanggan';
    }
}
