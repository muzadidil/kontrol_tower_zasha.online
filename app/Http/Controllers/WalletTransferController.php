<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\WalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletTransferController extends Controller
{
    public function transfer(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:mitras,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $sender = Mitra::where('id', auth()->user()->mitra_id)->first();
        if (!$sender) {
            return back()->with('error', 'Anda bukan Mitra.');
        }

        if ($sender->id === $request->receiver_id) {
            return back()->with('error', 'Tidak bisa transfer ke diri sendiri.');
        }

        if ($sender->saldo < $request->amount) {
            return back()->with('error', 'Saldo tidak mencukupi.');
        }

        DB::beginTransaction();
        try {
            $receiver = Mitra::findOrFail($request->receiver_id);

            $sender->decrement('saldo', $request->amount);
            $receiver->increment('saldo', $request->amount);

            WalletTransfer::create([
                'sender_mitra_id' => $sender->id,
                'receiver_mitra_id' => $receiver->id,
                'amount' => $request->amount,
                'description' => $request->description,
            ]);

            DB::commit();
            return back()->with('success', 'Transfer berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
