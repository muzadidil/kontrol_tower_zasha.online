<?php

namespace App\Models;

use App\Enums\WfhOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class WfhOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_code', 'pelanggan_id', 'mitra_id', 'mitra_layanan_id',
        'brief_description', 'reference_url', 'quantity',
        'unit_price', 'total_price', 'komisi_zasha', 'pendapatan_mitra',
        'tipe_order', 'deadline_at', 'status',
        'mitra_notified_at', 'mitra_responded_at', 'alasan_penolakan', 'auto_reject_at',
        'result_file_url', 'file_submitted_at', 'auto_release_at',
        'selesai_at', 'trigger_selesai',
    ];

    protected $casts = [
        'status'             => WfhOrderStatus::class,
        'deadline_at'        => 'datetime',
        'mitra_notified_at'  => 'datetime',
        'auto_reject_at'     => 'datetime',
        'file_submitted_at'  => 'datetime',
        'auto_release_at'    => 'datetime',
        'selesai_at'         => 'datetime',
    ];

    public function pelanggan(): BelongsTo { return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'id_pelanggan'); }
    public function mitra(): BelongsTo { return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra'); }
    public function mitraLayanan(): BelongsTo { return $this->belongsTo(MitraLayanan::class); }
    public function escrowLedgers(): HasMany { return $this->hasMany(EscrowLedger::class); }

    public function transitionTo(WfhOrderStatus $new): void
    {
        if (! $this->status->canTransitionTo($new)) {
            throw new \LogicException("Tidak bisa ubah status [{$this->status->value}] ke [{$new->value}].");
        }
        $this->update(['status' => $new]);
    }

    public function isMitraTimedOut(): bool
    {
        return $this->status === WfhOrderStatus::MenungguMitra
            && $this->auto_reject_at?->isPast();
    }

    public function isAutoReleaseOverdue(): bool
    {
        return $this->status === WfhOrderStatus::MenungguKonfirmasi
            && $this->auto_release_at?->isPast();
    }

    public static function generateCode(): string
    {
        $d   = now()->format('Ymd');
        $seq = str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        return "WFH-{$d}-{$seq}";
    }
}
