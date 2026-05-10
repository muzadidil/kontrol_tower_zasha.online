<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraDokumen extends Model
{
    protected $fillable = [
        'mitra_id', 'tipe_dokumen', 'file_url', 'link_url',
        'is_visible', 'status', 'catatan_admin',
    ];

    protected $casts = ['is_visible' => 'boolean'];

    public function mitra(): BelongsTo { return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra'); }
}
