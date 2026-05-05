<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Mitra extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'nama_mitra', 'kategori', 'alamat', 'nomor_wa', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latest = self::latest('id')->first();
            $number = $latest ? (int) substr($latest->id, -4) + 1 : 1;
            $model->id = 'ZSH-MTR-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
