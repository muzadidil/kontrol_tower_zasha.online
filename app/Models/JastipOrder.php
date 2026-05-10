<?php

namespace App\Models;

use App\Enums\JastipOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class JastipOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_code', 'pelanggan_id', 'mitra_id',
        'delivery_address', 'delivery_lat', 'delivery_lng',
        'total_jarak_km', 'total_stops', 'tarif_per_km', 'biaya_stop',
        'ongkos_jasa', 'komisi_zasha', 'pendapatan_mitra',
        'estimasi_total_barang', 'actual_total_barang',
        'saldo_mitra_snapshot', 'cod_eligible', 'status',
        'mitra_notified_at', 'mitra_responded_at', 'alasan_penolakan', 'auto_reject_at',
        'pickup_started_at', 'delivery_started_at', 'delivered_at', 'selesai_at',
    ];

    protected $casts = [
        'status'      => JastipOrderStatus::class,
        'cod_eligible' => 'boolean',
    ];

    public function pelanggan(): BelongsTo { return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'id_pelanggan'); }
    public function mitra(): BelongsTo { return $this->belongsTo(Mitra::class, 'mitra_id', 'id_mitra'); }
    public function stops(): HasMany { return $this->hasMany(JastipStop::class)->orderBy('urutan'); }
    public function items(): HasMany { return $this->hasMany(JastipOrderItem::class)->orderBy('urutan'); }

    public function transitionTo(JastipOrderStatus $new): void
    {
        if (! $this->status->canTransitionTo($new)) {
            throw new \LogicException("Tidak bisa ubah status [{$this->status->value}] ke [{$new->value}].");
        }
        $this->update(['status' => $new]);
    }

    public function allItemsChecked(): bool { return $this->items()->where('is_checked', false)->doesntExist(); }
    public function grandTotalCod(): float { return (float) $this->actual_total_barang + (float) $this->ongkos_jasa; }

    public static function generateCode(): string
    {
        $d   = now()->format('Ymd');
        $seq = str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        return "JST-{$d}-{$seq}";
    }
}
