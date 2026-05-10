<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Layanan extends Model
{
    protected $table    = 'layanans';
    protected $fillable = ['kategori_id', 'layanan', 'provider_id', 'provider', 'harga', 'profit', 'status'];

    protected $casts = [
        'harga'  => 'integer',
        'profit' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}
