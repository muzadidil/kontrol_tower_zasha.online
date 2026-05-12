<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraSparepart extends Model
{
    protected $table = 'mitra_spareparts';

    protected $fillable = [
        'mitra_id', 'nama', 'kode', 'kategori', 'deskripsi',
        'harga', 'harga_modal', 'stok', 'stok_min',
        'satuan', 'foto_path', 'is_aktif',
    ];

    protected $casts = [
        'harga'       => 'decimal:2',
        'harga_modal' => 'decimal:2',
        'is_aktif'    => 'boolean',
    ];

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra');
    }

    /** Margin per unit (harga jual - modal). */
    public function margin(): float
    {
        return (float) $this->harga - (float) $this->harga_modal;
    }

    /** Status stok: habis | menipis | aman. */
    public function statusStok(): string
    {
        if ($this->stok <= 0) return 'habis';
        if ($this->stok <= $this->stok_min) return 'menipis';
        return 'aman';
    }
}
