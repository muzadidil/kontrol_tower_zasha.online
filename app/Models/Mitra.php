<?php

namespace App\Models;

use App\Enums\{MitraKategori, MitraStatus};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Mitra extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table      = 'mitra';
    protected $primaryKey = 'id_mitra';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        // existing columns
        'id_kategori', 'nama_panggilan', 'nama_asli', 'foto_mitra',
        'no_wa', 'password', 'status_mitra', 'status_verifikasi',
        'tarif_per_jam', 'tarif_per_hari', 'biaya_service_standar',
        'saldo', 'deskripsi_singkat', 'alamat', 'lat_mitra', 'lng_mitra',
        // new columns (migration 002)
        'kategori_kode', 'role_id', 'status_online', 'lokasi_label',
        'vehicle_name', 'vehicle_plate', 'vehicle_color', 'vehicle_photo',
        'sim_number', 'sim_expiry',
        'rating', 'rejection_rate', 'timeout_streak', 'offline_until',
        'saldo_mitra', 'fcm_token', 'selfie_live_photo', 'face_gimmick_passed',
    ];

    protected $casts = [
        'kategori_kode'     => MitraKategori::class,
        'status_verifikasi' => MitraStatus::class,
        'saldo_mitra'       => 'decimal:2',
        'rating'            => 'decimal:2',
        'rejection_rate'    => 'decimal:2',
        'sim_expiry'        => 'date',
        'offline_until'     => 'datetime',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function layanans(): HasMany { return $this->hasMany(MitraLayanan::class, 'mitra_id', 'id_mitra'); }
    public function dokumens(): HasMany { return $this->hasMany(MitraDokumen::class, 'mitra_id', 'id_mitra'); }
    public function wfhOrders(): HasMany { return $this->hasMany(WfhOrder::class, 'mitra_id', 'id_mitra'); }
    public function jastipOrders(): HasMany { return $this->hasMany(JastipOrder::class, 'mitra_id', 'id_mitra'); }
    public function tenagaOrders(): HasMany { return $this->hasMany(TenagaOrder::class, 'mitra_id', 'id_mitra'); }
    public function serviceOrders(): HasMany { return $this->hasMany(ServiceOrder::class, 'mitra_id', 'id_mitra'); }
    public function walletTransfersSent(): HasMany { return $this->hasMany(WalletTransfer::class, 'dari_mitra_id', 'id_mitra'); }
    public function walletTransfersReceived(): HasMany { return $this->hasMany(WalletTransfer::class, 'ke_mitra_id', 'id_mitra'); }
    public function withdrawals(): HasMany { return $this->hasMany(WithdrawalRequest::class, 'mitra_id', 'id_mitra'); }

    public function isAvailable(): bool
    {
        if ($this->status_online === 'offline' && $this->offline_until?->isFuture()) return false;
        return $this->status_online === 'online'
            && $this->status_verifikasi instanceof MitraStatus
            && $this->status_verifikasi->isActive();
    }

    public function isSimExpired(): bool
    {
        return $this->sim_expiry && $this->sim_expiry->isPast();
    }

    public function addSaldo(float $amount): void { $this->increment('saldo', $amount); }
    public function deductSaldo(float $amount): void { $this->decrement('saldo', $amount); }

    public function getNameAttribute(): string
    {
        return $this->nama_asli ?? $this->nama_panggilan ?? '';
    }

    // ─── Role & Feature Access ───
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function tarifs(): HasMany
    {
        return $this->hasMany(TarifMitra::class, 'mitra_id', 'id_mitra');
    }

    public function verifikasiDokumens(): HasMany
    {
        return $this->hasMany(MitraVerifikasi::class, 'mitra_id', 'id_mitra');
    }

    /**
     * Cek apakah mitra punya akses ke fitur tertentu via role-nya.
     * Mitra tanpa role = tidak punya akses fitur apapun.
     */
    public function hasFeature(string $key): bool
    {
        if (!$this->role_id) return false;
        return $this->role?->hasFeature($key) ?? false;
    }
}
