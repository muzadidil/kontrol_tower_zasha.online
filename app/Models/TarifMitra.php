<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifMitra extends Model
{
    protected $table = 'tarif_mitra';

    protected $fillable = [
        'mitra_id',
        'keterangan',
        'nominal',
        'satuan',
        'is_aktif',
    ];

    protected $casts = [
        'nominal'  => 'decimal:2',
        'is_aktif' => 'boolean',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    /** Harga yang dibayar pelanggan = tarif mitra + komisi Zasha. */
    public function hargaPelanggan(): float
    {
        $komisi = Setting::komisiPersen();
        return (float) $this->nominal * (1 + $komisi / 100);
    }

    /** Keuntungan admin per item tarif ini. */
    public function keuntunganAdmin(): float
    {
        return $this->hargaPelanggan() - (float) $this->nominal;
    }
}
