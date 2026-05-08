<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pelanggans';
    protected $fillable = ['nama_pelanggan', 'email', 'foto', 'is_verif', 'kode_zasha', 'saldo'];

    public function alamats()
    {
        return $this->hasMany(Alamat::class, 'id_pelanggan');
    }
}
