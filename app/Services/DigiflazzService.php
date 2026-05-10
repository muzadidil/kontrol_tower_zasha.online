<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    protected $username;
    protected $apiKey;

    public function __construct()
    {
        // Selalu baca dari DB Setting agar admin bisa ubah lewat panel
        $this->username = Setting::get('digiflazz_username', '');
        $this->apiKey   = Setting::get('digiflazz_api_key', '');
    }

    protected function generateSignature($refId)
    {
        return md5($this->username . $this->apiKey . $refId);
    }

    public function checkBalance()
    {
        $payload = [
            'cmd' => 'deposit',
            'username' => $this->username,
            'sign' => md5($this->username . $this->apiKey),
        ];

        return Http::post('https://api.digiflazz.com/v1/cek-saldo', $payload)->json();
    }

    public function purchase($sku, $target, $refId)
    {
        $payload = [
            'username' => $this->username,
            'buyer_sku_code' => $sku,
            'customer_no' => $target,
            'ref_id' => $refId,
            'sign' => $this->generateSignature($refId),
        ];

        return Http::post('https://api.digiflazz.com/v1/transaction', $payload)->json();
    }

    /**
     * Fetch semua produk prepaid dari Digiflazz pricelist.
     * Signature: md5($username . $apiKey . 'pricelist')
     *
     * @return array  List of products with 'brand', 'product_name', 'buyer_sku_code', 'price', dst.
     * @throws \RuntimeException jika API call gagal atau response tidak valid.
     */
    public function pricelist(): array
    {
        if (empty($this->username) || empty($this->apiKey)) {
            throw new \RuntimeException('Kredensial Digiflazz belum dikonfigurasi. Cek Pengaturan → API.');
        }

        $payload = [
            'cmd'      => 'prepaid',
            'username' => $this->username,
            'sign'     => md5($this->username . $this->apiKey . 'pricelist'),
        ];

        try {
            $response = Http::timeout(60)->post('https://api.digiflazz.com/v1/price-list', $payload);

            if (! $response->successful()) {
                Log::error('Digiflazz pricelist HTTP error', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \RuntimeException('Gagal fetch pricelist: HTTP ' . $response->status());
            }

            $json = $response->json();

            if (! isset($json['data']) || ! is_array($json['data'])) {
                Log::error('Digiflazz pricelist response invalid', ['response' => $json]);
                throw new \RuntimeException('Format response Digiflazz tidak valid.');
            }

            return $json['data'];
        } catch (\Exception $e) {
            Log::error('Digiflazz pricelist exception', ['message' => $e->getMessage()]);
            throw new \RuntimeException('Error koneksi Digiflazz: ' . $e->getMessage());
        }
    }
}
