<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    protected $username;
    protected $apiKey;

    public function __construct()
    {
        $this->username = config('services.digiflazz.username');
        $this->apiKey = config('services.digiflazz.api_key');
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
}
