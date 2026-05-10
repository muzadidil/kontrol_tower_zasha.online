<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MitraLayanan extends Model
{
    protected $fillable = [
        'mitra_id', 'master_layanan_id',
        'tarif_per_jam', 'tarif_per_hari', 'tarif_per_km',
        'tarif_per_unit', 'satuan_unit', 'tarif_per_project',
        'express_multiplier', 'biaya_service_standar', 'tarif_bensin_per_km',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function mitra(): BelongsTo { return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra'); }
    public function masterLayanan(): BelongsTo { return $this->belongsTo(MasterLayanan::class); }
}
