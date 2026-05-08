<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pelanggans';
    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'nama_pelanggan',
        'email',
        'password',
        'no_wa',
        'tgl_lahir',
        'foto',
        'kode_zasha',
        'saldo',
        'status_verifikasi'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function alamats()
    {
        return $this->hasMany(Alamat::class, 'id_pelanggan');
    }
}
