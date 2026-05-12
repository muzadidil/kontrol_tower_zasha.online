<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderFotoBukti extends Model
{
    protected $table = 'order_foto_bukti';

    protected $fillable = [
        'tracking_id', 'mitra_id', 'tipe', 'file_path', 'catatan',
    ];

    public const TIPE_LABEL = [
        'sebelum' => 'Sebelum Dikerjakan',
        'proses'  => 'Sedang Dikerjakan',
        'sesudah' => 'Setelah Selesai',
    ];

    public function tracking(): BelongsTo
    {
        return $this->belongsTo(OrderTracking::class, 'tracking_id');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    public function getTipeLabelAttribute(): string
    {
        return self::TIPE_LABEL[$this->tipe] ?? ucfirst($this->tipe);
    }
}
