<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'mitra_id', 'jumlah', 'nama_rekening', 'nomor_rekening', 'nama_bank',
        'status', 'catatan_admin', 'approved_by', 'processed_at',
    ];

    protected $casts = [
        'jumlah'       => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function mitra(): BelongsTo { return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra'); }
}
