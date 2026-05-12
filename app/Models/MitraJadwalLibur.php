<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraJadwalLibur extends Model
{
    protected $table = 'mitra_jadwal_libur';

    protected $fillable = [
        'mitra_id',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }
}
