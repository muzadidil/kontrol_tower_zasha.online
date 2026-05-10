<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table    = 'kategoris';
    protected $fillable = ['nama', 'sub_nama', 'kode', 'thumbnail', 'server_id', 'tipe', 'status'];

    protected $casts = [
        'server_id' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'kode';
    }

    public function layanans(): HasMany
    {
        return $this->hasMany(Layanan::class)->where('status', 'available')->orderBy('harga');
    }

    public function butuhServerId(): bool
    {
        return $this->server_id === 1;
    }
}
