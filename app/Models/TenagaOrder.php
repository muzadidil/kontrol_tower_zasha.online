<?php

namespace App\Models;

use App\Enums\TenagaOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenagaOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_code', 'pelanggan_id', 'mitra_id', 'mitra_layanan_id',
        'tipe_waktu', 'jadwal_at', 'tipe_durasi', 'durasi', 'tarif', 'total_biaya',
        'komisi_zasha', 'pendapatan_mitra',
        'metode_pembayaran', 'saldo_mitra_snapshot', 'cod_eligible',
        'alamat_pelanggan', 'pelanggan_lat', 'pelanggan_lng', 'keterangan_kerja',
        'status',
        'mitra_notified_at', 'mitra_responded_at', 'alasan_penolakan', 'auto_reject_at',
        'berangkat_at', 'mulai_kerja_at', 'selesai_at',
    ];

    protected $casts = [
        'status'       => TenagaOrderStatus::class,
        'cod_eligible' => 'boolean',
        'jadwal_at'    => 'datetime',
    ];

    public function pelanggan(): BelongsTo { return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'id_pelanggan'); }
    public function mitra(): BelongsTo { return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra'); }
    public function mitraLayanan(): BelongsTo { return $this->belongsTo(MitraLayanan::class); }

    public function transitionTo(TenagaOrderStatus $new): void
    {
        if (! $this->status->canTransitionTo($new)) {
            throw new \LogicException("Tidak bisa ubah status [{$this->status->value}] ke [{$new->value}].");
        }
        $this->update(['status' => $new]);
    }

    public static function generateCode(): string
    {
        $d   = now()->format('Ymd');
        $seq = str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        return "TNG-{$d}-{$seq}";
    }
}
