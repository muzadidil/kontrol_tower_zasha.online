<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Mitra extends Authenticatable
{
    use Notifiable;

    protected $table      = 'mitra';
    protected $primaryKey = 'id_mitra';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_kategori', 'nama_panggilan', 'nama_asli', 'foto_mitra',
        'no_wa', 'password', 'status_mitra', 'status_verifikasi',
        'tarif_per_jam', 'tarif_per_hari', 'biaya_service_standar',
        'saldo', 'deskripsi_singkat', 'alamat',
        'lat_mitra', 'lng_mitra',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function walletTransfersSent()
    {
        return $this->hasMany(WalletTransfer::class, 'sender_mitra_id', 'id_mitra');
    }

    public function walletTransfersReceived()
    {
        return $this->hasMany(WalletTransfer::class, 'receiver_mitra_id', 'id_mitra');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'mitra_id', 'id_mitra');
    }
}
