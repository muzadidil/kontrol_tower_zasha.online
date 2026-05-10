<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class JastipStop extends Model
{
    protected $fillable = [
        'jastip_order_id', 'urutan', 'nama_lokasi', 'alamat_lokasi',
        'lat', 'lng', 'jarak_dari_prev_km', 'tiba_at',
    ];

    protected $casts = ['tiba_at' => 'datetime'];

    public function order(): BelongsTo { return $this->belongsTo(JastipOrder::class, 'jastip_order_id'); }
    public function items(): HasMany { return $this->hasMany(JastipOrderItem::class)->orderBy('urutan'); }
}
