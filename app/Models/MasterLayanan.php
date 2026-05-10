<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterLayanan extends Model
{
    protected $fillable = ['kategori_mitra', 'nama_layanan', 'deskripsi', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function mitraLayanans(): HasMany { return $this->hasMany(MitraLayanan::class); }
}
