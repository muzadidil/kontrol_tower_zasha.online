<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $table = 'alamats';
    protected $fillable = [
        'id_pelanggan',
        'alamat',
        'latitude',
        'longitude',
        'is_utama',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
}
