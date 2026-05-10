<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransfer extends Model
{
    protected $fillable = ['dari_mitra_id', 'ke_mitra_id', 'jumlah', 'catatan', 'status'];

    protected $casts = ['jumlah' => 'decimal:2'];

    public function dariMitra(): BelongsTo { return $this->belongsTo(Mitra::class, 'dari_mitra_id', 'id_mitra'); }
    public function keMitra(): BelongsTo { return $this->belongsTo(Mitra::class, 'ke_mitra_id', 'id_mitra'); }
}
