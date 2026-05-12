<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraVerifikasi extends Model
{
    protected $table = 'mitra_verifikasi';

    protected $fillable = [
        'mitra_id',
        'verifikasi_key',
        'status',
        'file_path',
        'catatan',
    ];

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DITOLAK  = 'ditolak';

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    public function getLabelAttribute(): string
    {
        return Role::ALL_VERIFIKASI[$this->verifikasi_key] ?? $this->verifikasi_key;
    }
}
