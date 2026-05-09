<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $table = 'alamats';
    protected $fillable = [
        'id_pelanggan',
        'label_alamat',
        'nama_penerima',
        'no_wa_penerima',
        'alamat_lengkap',
        'is_utama',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
}
