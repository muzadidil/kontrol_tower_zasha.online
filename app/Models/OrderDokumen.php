<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDokumen extends Model
{
    protected $table = 'order_dokumen';

    protected $fillable = [
        'tracking_id', 'mitra_id', 'judul', 'file_path',
        'mime_type', 'size_bytes', 'catatan', 'is_final',
    ];

    protected $casts = [
        'is_final' => 'boolean',
    ];

    public function tracking(): BelongsTo
    {
        return $this->belongsTo(OrderTracking::class, 'tracking_id');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    public function getSizeFormattedAttribute(): string
    {
        $size = (int) $this->size_bytes;
        if ($size < 1024) return $size . ' B';
        if ($size < 1024 * 1024) return round($size / 1024, 1) . ' KB';
        return round($size / (1024 * 1024), 1) . ' MB';
    }

    public function getIconClassAttribute(): string
    {
        $mime = strtolower((string) $this->mime_type);
        if (str_contains($mime, 'pdf')) return 'bi-file-earmark-pdf';
        if (str_contains($mime, 'image')) return 'bi-file-earmark-image';
        if (str_contains($mime, 'word') || str_contains($mime, 'document')) return 'bi-file-earmark-word';
        if (str_contains($mime, 'sheet') || str_contains($mime, 'excel')) return 'bi-file-earmark-excel';
        if (str_contains($mime, 'zip') || str_contains($mime, 'rar')) return 'bi-file-earmark-zip';
        if (str_contains($mime, 'video')) return 'bi-file-earmark-play';
        if (str_contains($mime, 'audio')) return 'bi-file-earmark-music';
        return 'bi-file-earmark';
    }
}
