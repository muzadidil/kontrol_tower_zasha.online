<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    // Mitra functions
    public function index()
    {
        $withdrawals = Withdrawal::where('mitra_id', auth('mitra')->id())->latest()->get();
        return view('admin.finance.withdrawal', compact('withdrawals'));
    }

    public function request(Request $request)
    {
        $request->validate([
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:100',
            'nominal'        => 'required|numeric|min:50000|max:10000000',
        ]);

        DB::beginTransaction();
        try {
            // lockForUpdate() mencegah race condition: dua request bersamaan tidak bisa baca saldo yang sama
            $mitra = Mitra::where('id_mitra', auth('mitra')->id())->lockForUpdate()->first();
            if (!$mitra || $mitra->saldo < $request->nominal) {
                DB::rollBack();
                return back()->with('error', 'Saldo tidak mencukupi.');
            }

            $mitra->decrement('saldo', $request->nominal);
            Withdrawal::create([
                'mitra_id'       => $mitra->id_mitra,
                'bank_name'      => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name'   => $request->account_name,
                'nominal'        => $request->nominal,
                'status'         => 'pending',
            ]);
            DB::commit();
            return back()->with('success', 'Request penarikan berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan.');
        }
    }

    // Admin functions
    public function adminIndex()
    {
        $withdrawals = Withdrawal::with('mitra')->where('status', 'pending')->get();
        return view('admin.finance.withdrawal_admin', compact('withdrawals'));
    }

    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->update(['status' => 'approved']);
        return back()->with('success', 'Penarikan disetujui.');
    }

    public function reject($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        if ($withdrawal->status === 'pending') {
            DB::beginTransaction();
            try {
                $withdrawal->update(['status' => 'rejected']);
                $mitra = Mitra::findOrFail($withdrawal->mitra_id);
                $mitra->increment('saldo', $withdrawal->nominal);
                DB::commit();
                return back()->with('success', 'Penarikan ditolak, saldo dikembalikan.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Terjadi kesalahan.');
            }
        }
        return back()->with('error', 'Tidak bisa menolak penarikan ini.');
    }
}
