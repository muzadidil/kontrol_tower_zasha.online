<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Mitra extends Authenticatable
{
    use Notifiable;

    public $incrementing = false;
    protected $keyType   = 'string';
    protected $table     = 'mitras';

    protected $fillable = [
        'id', 'nama_panggilan', 'usia', 'kategori', 'deskripsi_singkat',
        'nomor_wa', 'password', 'saldo', 'saldo_mitra', 'tarif_per_jam', 'tarif_per_hari',
        'status', 'portfolio_link', 'is_wfh',
        'tarif_per_km', 'biaya_service_standar', 'tarif_bensin_per_km_service',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $latest = self::latest('created_at')->first();
                $number = $latest ? (int) substr($latest->id, -4) + 1 : 1;
                $model->id = 'ZSH-MTR-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Accessor agar auth bisa pakai kolom nomor_wa sebagai username
    public function getAuthIdentifierName(): string
    {
        return 'id';
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
