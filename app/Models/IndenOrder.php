<?php

namespace App\Models;

use App\Enums\IndenOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndenOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_code', 'pelanggan_id', 'mitra_id',
        'tanggal_pelaksanaan', 'tipe_durasi', 'durasi', 'tarif',
        'total_biaya', 'dp_amount', 'pelunasan_amount', 'komisi_zasha', 'pendapatan_mitra',
        'metode_pembayaran', 'alamat_pelanggan', 'pelanggan_lat', 'pelanggan_lng', 'keterangan_kerja',
        'status', 'mitra_responded_at', 'alasan_penolakan', 'auto_reject_at',
        'dp_paid_at', 'pelunasan_paid_at', 'selesai_at', 'auto_konfirmasi_at',
    ];

    protected $casts = [
        'status'                  => IndenOrderStatus::class,
        'tanggal_pelaksanaan'     => 'date',
        'mitra_responded_at'      => 'datetime',
        'auto_reject_at'          => 'datetime',
        'dp_paid_at'              => 'datetime',
        'pelunasan_paid_at'       => 'datetime',
        'selesai_at'              => 'datetime',
        'auto_konfirmasi_at'      => 'datetime',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'id_pelanggan');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'id');
    }

    public function transitionTo(IndenOrderStatus $new): void
    {
        if (!$this->status->canTransitionTo($new)) {
            throw new \LogicException("Tidak bisa ubah status [{$this->status->value}] ke [{$new->value}].");
        }
        $this->update(['status' => $new]);
    }

    public static function generateCode(): string
    {
        $d   = now()->format('Ymd');
        $seq = str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        return "IND-{$d}-{$seq}";
    }
}
