<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JastipOrderItem extends Model
{
    protected $fillable = [
        'jastip_order_id', 'jastip_stop_id', 'urutan',
        'nama_barang', 'harga_perkiraan', 'harga_asli',
        'is_checked', 'checked_at', 'catatan',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function order(): BelongsTo { return $this->belongsTo(JastipOrder::class, 'jastip_order_id'); }
    public function stop(): BelongsTo { return $this->belongsTo(JastipStop::class, 'jastip_stop_id'); }
}
