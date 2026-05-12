<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'order_type', 'order_id', 'penilai_id', 'tipe_penilai',
        'dinilai_id', 'tipe_dinilai', 'bintang', 'ulasan', 'foto_url', 'is_anonim',
        'balasan_mitra', 'balasan_at',
    ];

    protected $casts = [
        'is_anonim'  => 'boolean',
        'balasan_at' => 'datetime',
    ];
}
