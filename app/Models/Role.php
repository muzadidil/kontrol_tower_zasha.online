<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'icon_color', 'is_default', 'is_active'];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    /** Daftar icon Bootstrap Icons yang sering dipakai untuk role mitra. */
    public const SUGGESTED_ICONS = [
        'bi-tools'           => 'Tools (TNG)',
        'bi-truck'           => 'Truck (JST)',
        'bi-laptop'          => 'Laptop (WFH)',
        'bi-wrench-adjustable'=> 'Wrench (SVC)',
        'bi-person-badge'    => 'Badge',
        'bi-shield-check'    => 'Shield',
        'bi-star-fill'       => 'Star',
        'bi-lightning-fill'  => 'Lightning',
        'bi-house-fill'      => 'House',
        'bi-bag-fill'        => 'Bag',
        'bi-gear-fill'       => 'Gear',
        'bi-globe'           => 'Globe',
    ];

    public const DEFAULT_ICON = 'bi-shield-fill';
    public const DEFAULT_ICON_COLOR = '#005aa9';

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
