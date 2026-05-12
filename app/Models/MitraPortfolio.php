<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraPortfolio extends Model
{
    protected $table = 'mitra_portfolios';

    protected $fillable = [
        'mitra_id',
        'judul',
        'deskripsi',
        'file_path',
        'link_url',
        'kategori',
        'is_featured',
        'urutan',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    public function isImage(): bool
    {
        if (!$this->file_path) return false;
        $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }
}
