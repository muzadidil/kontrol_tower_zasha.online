<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraNotifikasi extends Model
{
    use HasFactory;

    protected $table = 'mitra_notifikasis';

    protected $fillable = [
        'mitra_id',
        'tracking_id',
        'tipe',
        'judul',
        'pesan',
        'is_read',
        'is_push_sent',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_push_sent' => 'boolean',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    public function tracking(): BelongsTo
    {
        return $this->belongsTo(OrderTracking::class, 'tracking_id');
    }
}
