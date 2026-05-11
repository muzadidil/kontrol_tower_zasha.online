<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderTracking extends Model
{
    use HasFactory;

    protected $table = 'order_trackings';

    protected $fillable = [
        'order_type',
        'order_id',
        'mitra_id',
        'pelanggan_id',
        'status',
        'pesan_tolak',
        'harga_jual',
        'harga_modal',
        'komisi_zasha',
        'escrow_status',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'id_pelanggan');
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(MitraNotifikasi::class, 'tracking_id');
    }
}
