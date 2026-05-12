<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Master daftar semua fitur yang bisa diatur per role.
     * Edit di sini saat menambah fitur baru.
     */
    public const ALL_FEATURES = [
        // Order modules
        'order-tenaga'  => 'Terima Order Tenaga',
        'order-jastip'  => 'Terima Order Jastip',
        'order-wfh'     => 'Terima Order WFH/Digital',
        'order-service' => 'Terima Order Service/Teknisi',
        'order-inden'   => 'Terima Order Inden (Booking)',

        // Tools
        'maps'          => 'Akses Peta & Stops',
        'sparepart'     => 'Kelola Sparepart',
        'portfolio'     => 'Upload Portfolio',

        // Common
        'saldo'         => 'Akses Saldo & Withdraw',
        'profil'        => 'Edit Profil',
        'pesanan-list'  => 'Lihat Daftar Pesanan',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(RoleFeature::class);
    }

    public function hasFeature(string $key): bool
    {
        return $this->features()->where('feature_key', $key)->exists();
    }

    /** Sync daftar fitur untuk role ini (replace semua). */
    public function syncFeatures(array $featureKeys): void
    {
        $this->features()->delete();
        foreach ($featureKeys as $key) {
            if (array_key_exists($key, self::ALL_FEATURES)) {
                $this->features()->create(['feature_key' => $key]);
            }
        }
    }
}
