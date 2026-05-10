<?php

namespace App\Services;

use App\Models\{Mitra, Pelanggan, TopupRequest};
use Illuminate\Support\Facades\{DB, Http};
use Illuminate\Support\Str;

class TokopayService
{
    protected string $merchantId;
    protected string $secret;
    protected string $apiUrl;

    public function __construct()
    {
        $this->merchantId = config('services.tokopay.merchant_id', '');
        $this->secret     = config('services.tokopay.secret', '');
        $this->apiUrl     = config('services.tokopay.url', 'https://api.tokopay.id/v1/order');
    }

    /**
     * Buat request topup dan ambil URL pembayaran dari Tokopay.
     *
     * @param  string $userType 'pelanggan' | 'mitra'
     * @param  int    $userId
     * @param  float  $jumlah
     * @return array  ['topup' => TopupRequest, 'pay_url' => string]
     */
    public function createTopup(string $userType, int $userId, float $jumlah): array
    {
        $refId = 'TPU-' . strtoupper(Str::random(8));

        $topup = TopupRequest::create([
            'user_type'  => $userType,
            'user_id'    => $userId,
            'jumlah'     => $jumlah,
            'tokopay_ref'=> $refId,
            'status'     => 'pending',
        ]);

        // Panggil Tokopay (placeholder; ganti dengan real API)
        $signature = md5($this->merchantId . $this->secret . $refId);

        $response = Http::asForm()->post($this->apiUrl, [
            'merchant'   => $this->merchantId,
            'kode_channel' => 'QRIS',
            'reff_id'    => $refId,
            'nominal'    => (int) $jumlah,
            'signature'  => $signature,
        ])->json();

        $payUrl = $response['data']['pay_url'] ?? null;

        $topup->update(['tokopay_response' => $response]);

        return [
            'topup'   => $topup,
            'pay_url' => $payUrl,
            'qr_url'  => $response['data']['qr_url'] ?? null,
        ];
    }

    /**
     * Handle webhook dari Tokopay (dipanggil saat pembayaran sukses).
     */
    public function handleWebhook(array $data): void
    {
        $topup = TopupRequest::where('tokopay_ref', $data['reff_id'] ?? null)->first();
        if (! $topup || $topup->status !== 'pending') return;

        DB::transaction(function () use ($topup, $data) {
            $status = strtolower($data['status'] ?? '');

            if ($status === 'success' || $status === 'paid') {
                $topup->update([
                    'status'           => 'success',
                    'tokopay_response' => array_merge((array) $topup->tokopay_response, $data),
                    'paid_at'          => now(),
                ]);

                // Tambah saldo ke user
                if ($topup->user_type === 'mitra') {
                    Mitra::where('id_mitra', $topup->user_id)->increment('saldo', $topup->jumlah);
                } else {
                    Pelanggan::where('id_pelanggan', $topup->user_id)->increment('saldo', $topup->jumlah);
                }
            } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
                $topup->update([
                    'status'           => 'failed',
                    'tokopay_response' => array_merge((array) $topup->tokopay_response, $data),
                ]);
            }
        });
    }
}
