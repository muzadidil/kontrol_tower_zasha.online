<?php

namespace App\Http\Controllers;

use App\Models\PpobTransaction;
use App\Models\Pelanggan;
use App\Models\Mitra;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PpobController extends Controller
{
    protected $digiflazz;

    public function __construct(DigiflazzService $digiflazz)
    {
        $this->digiflazz = $digiflazz;
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'sku' => 'required',
            'target_number' => 'required',
            'selling_price' => 'required|numeric',
        ]);

        $user = $request->user();
        $userModel = ($user->role === 'mitra') ? Mitra::where('user_id', $user->id)->first() : Pelanggan::where('user_id', $user->id)->first();

        if ($userModel->saldo < $request->selling_price) {
            return response()->json(['message' => 'Saldo tidak cukup'], 400);
        }

        DB::beginTransaction();
        try {
            $userModel->decrement('saldo', $request->selling_price);

            $refId = 'ZSH-PPOB-' . strtoupper(Str::random(8));
            $response = $this->digiflazz->purchase($request->sku, $request->target_number, $refId);

            if (isset($response['data']['status']) && $response['data']['status'] == 'Gagal') {
                $userModel->increment('saldo', $request->selling_price);
                DB::rollBack();
                return response()->json(['message' => 'Transaksi Gagal', 'detail' => $response], 400);
            }

            PpobTransaction::create([
                'id' => $refId,
                'user_id' => $user->id,
                'user_type' => $user->role,
                'sku' => $request->sku,
                'target_number' => $request->target_number,
                'price' => $response['data']['price'] ?? 0,
                'selling_price' => $request->selling_price,
                'status' => 'Pending',
                'ref_id' => $refId,
            ]);

            DB::commit();
            return response()->json(['message' => 'Transaksi diproses', 'data' => $response]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error sistem'], 500);
        }
    }

    public function webhook(Request $request)
    {
        $signature = $request->header('X-Hub-Signature');
        // Logic to verify signature using DIGIFLAZZ_WEBHOOK_SECRET
        
        $data = $request->data;
        $transaction = PpobTransaction::where('ref_id', $data['ref_id'])->first();

        if ($transaction && $data['status'] == 'Gagal') {
            $userModel = ($transaction->user_type === 'mitra') ? Mitra::where('user_id', $transaction->user_id)->first() : Pelanggan::where('user_id', $transaction->user_id)->first();
            $userModel->increment('saldo', $transaction->selling_price);
            $transaction->update(['status' => 'Failed']);
        } elseif ($transaction) {
            $transaction->update(['status' => $data['status'], 'sn' => $data['sn']]);
        }

        return response()->json(['message' => 'Success']);
    }
}
