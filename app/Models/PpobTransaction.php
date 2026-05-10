<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpobTransaction extends Model
{
    protected $fillable = [
        'user_type', 'user_id', 'jenis_produk', 'nomor_tujuan',
        'kode_produk', 'nama_produk', 'harga_modal', 'harga_jual', 'margin_zasha',
        'digiflazz_ref', 'sn', 'status', 'digiflazz_response', 'processed_at',
    ];

    protected $casts = [
        'digiflazz_response' => 'array',
        'processed_at'       => 'datetime',
    ];
}
