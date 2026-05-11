<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\WalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletTransferController extends Controller
{
    public function transfer(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:mitra,id_mitra',
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $sender = Auth::guard('mitra')->user();
        if (! $sender) {
            return back()->with('error', 'Anda bukan Mitra.');
        }

        if ((int) $sender->id_mitra === (int) $request->receiver_id) {
            return back()->with('error', 'Tidak bisa transfer ke diri sendiri.');
        }

        if ($sender->saldo < $request->amount) {
            return back()->with('error', 'Saldo tidak mencukupi.');
        }

        try {
            DB::transaction(function () use ($sender, $request) {
                $receiver = Mitra::lockForUpdate()->findOrFail($request->receiver_id);
                $senderLocked = Mitra::lockForUpdate()->findOrFail($sender->id_mitra);

                if ($senderLocked->saldo < $request->amount) {
                    throw new \RuntimeException('Saldo tidak mencukupi.');
                }

                $senderLocked->decrement('saldo', $request->amount);
                $receiver->increment('saldo', $request->amount);

                WalletTransfer::create([
                    'dari_mitra_id' => $senderLocked->id_mitra,
                    'ke_mitra_id'   => $receiver->id_mitra,
                    'jumlah'        => $request->amount,
                    'catatan'       => $request->description,
                    'status'        => 'berhasil',
                ]);
            });

            return back()->with('success', 'Transfer berhasil.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Wallet transfer error', [
                'message'     => $e->getMessage(),
                'sender_id'   => $sender->id_mitra,
                'receiver_id' => $request->receiver_id,
                'amount'      => $request->amount,
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memproses transfer.');
        }
    }
}
