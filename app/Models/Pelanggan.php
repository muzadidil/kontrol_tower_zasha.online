<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'nama_lengkap', 'nomor_wa', 'alamat_utama', 'saldo_pelanggan'];

    protected static function booted()
    {
        static::creating(function ($model) {
            $latest = self::latest('id')->first();
            $number = $latest ? (int) substr($latest->id, -4) + 1 : 1;
            $model->id = 'ZSH-PLG-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
