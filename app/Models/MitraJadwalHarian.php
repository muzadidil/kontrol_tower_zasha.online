<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraJadwalHarian extends Model
{
    protected $table = 'mitra_jadwal_harian';

    protected $fillable = [
        'mitra_id',
        'hari',
        'is_libur',
        'jam_buka',
        'jam_tutup',
    ];

    protected $casts = [
        'is_libur' => 'boolean',
    ];

    public const HARI = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    public function getNamaHariAttribute(): string
    {
        return self::HARI[$this->hari] ?? 'Unknown';
    }
}
