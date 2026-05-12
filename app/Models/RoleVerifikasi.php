<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleVerifikasi extends Model
{
    protected $table = 'role_verifikasi';

    protected $fillable = [
        'role_id',
        'verifikasi_key',
        'wajib',
    ];

    protected $casts = [
        'wajib' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** Label tampilan dari Role::ALL_VERIFIKASI */
    public function getLabelAttribute(): string
    {
        return Role::ALL_VERIFIKASI[$this->verifikasi_key] ?? $this->verifikasi_key;
    }
}
