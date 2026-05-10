<?php

namespace App\Services;

use App\Models\{Mitra, Pelanggan, PpobTransaction};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PpobService
{
    public function __construct(private DigiflazzService $digiflazz) {}

    /**
     * Buat transaksi PPOB.
     *
     * @param  array  $data  ['jenis_produk', 'nomor_tujuan', 'kode_produk', 'nama_produk', 'harga_modal', 'harga_jual']
     * @param  string $userType  'pelanggan' | 'mitra'
     * @param  int    $userId
     */
    public function createTransaction(array $data, string $userType, int $userId): PpobTransaction
    {
        return DB::transaction(function () use ($data, $userType, $userId) {
            $hargaJual = (float) $data['harga_jual'];
            $hargaModal = (float) $data['harga_modal'];
            $marginZasha = $hargaJual - $hargaModal;

            // Cek saldo user
            $user = $userType === 'mitra'
                ? Mitra::where('id_mitra', $userId)->lockForUpdate()->firstOrFail()
                : Pelanggan::where('id_pelanggan', $userId)->lockForUpdate()->firstOrFail();

            if ($user->saldo < $hargaJual) {
                throw new \RuntimeException('Saldo tidak cukup untuk transaksi ini.');
            }

            // Potong saldo dulu
            $user->decrement('saldo', $hargaJual);

            // Buat record transaksi
            $refId = 'ZSH-' . strtoupper(Str::random(8));
            $trx = PpobTransaction::create([
                'user_type'      => $userType,
                'user_id'        => $userId,
                'jenis_produk'   => $data['jenis_produk'],
                'nomor_tujuan'   => $data['nomor_tujuan'],
                'kode_produk'    => $data['kode_produk'],
                'nama_produk'    => $data['nama_produk'],
                'harga_modal'    => $hargaModal,
                'harga_jual'     => $hargaJual,
                'margin_zasha'   => $marginZasha,
                'digiflazz_ref'  => $refId,
                'status'         => 'pending',
            ]);

            // Trigger Digiflazz (di luar transaksi DB)
            return $trx;
        });
    }

    public function callDigiflazz(PpobTransaction $trx): array
    {
        try {
            $response = $this->digiflazz->purchase(
                $trx->kode_produk,
                $trx->nomor_tujuan,
                $trx->digiflazz_ref
            );

            $status = $response['data']['status'] ?? 'pending';

            if (strtolower($status) === 'gagal') {
                $this->refund($trx, 'Transaksi gagal di Digiflazz');
                $trx->update([
                    'status'             => 'gagal',
                    'digiflazz_response' => $response,
                    'processed_at'       => now(),
                ]);
            } elseif (strtolower($status) === 'sukses') {
                $trx->update([
                    'status'             => 'sukses',
                    'sn'                 => $response['data']['sn'] ?? null,
                    'digiflazz_response' => $response,
                    'processed_at'       => now(),
                ]);
            } else {
                $trx->update(['digiflazz_response' => $response]);
            }

            return $response;
        } catch (\Exception $e) {
            $this->refund($trx, 'Sistem error: ' . $e->getMessage());
            $trx->update(['status' => 'gagal', 'processed_at' => now()]);
            throw $e;
        }
    }

    public function handleWebhook(array $data): void
    {
        $trx = PpobTransaction::where('digiflazz_ref', $data['ref_id'])->first();
        if (! $trx || $trx->status !== 'pending') return;

        DB::transaction(function () use ($trx, $data) {
            if (strtolower($data['status']) === 'sukses') {
                $trx->update([
                    'status'             => 'sukses',
                    'sn'                 => $data['sn'] ?? null,
                    'digiflazz_response' => $data,
                    'processed_at'       => now(),
                ]);
            } elseif (strtolower($data['status']) === 'gagal') {
                $this->refund($trx, 'Transaksi gagal (webhook)');
                $trx->update([
                    'status'             => 'gagal',
                    'digiflazz_response' => $data,
                    'processed_at'       => now(),
                ]);
            }
        });
    }

    private function refund(PpobTransaction $trx, string $reason): void
    {
        if ($trx->user_type === 'mitra') {
            Mitra::where('id_mitra', $trx->user_id)->increment('saldo', $trx->harga_jual);
        } else {
            Pelanggan::where('id_pelanggan', $trx->user_id)->increment('saldo', $trx->harga_jual);
        }
    }
}
