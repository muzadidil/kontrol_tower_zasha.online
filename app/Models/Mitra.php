<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Mitra extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'nama_panggilan', 'usia', 'kategori', 'deskripsi_singkat', 'nomor_wa', 'saldo', 'tarif_per_jam', 'tarif_per_hari', 'status', 'portfolio_link', 'is_wfh', 'tarif_per_km', 'biaya_service_standar', 'tarif_bensin_per_km_service'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $latest = self::latest('id')->first();
            $number = $latest ? (int) substr($latest->id, -4) + 1 : 1;
            $model->id = 'ZSH-MTR-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function walletTransfersSent()
    {
        return $this->hasMany(WalletTransfer::class, 'sender_mitra_id');
    }

    public function walletTransfersReceived()
    {
        return $this->hasMany(WalletTransfer::class, 'receiver_mitra_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'mitra_id');
    }
}
