<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'pelanggan_id', 'mitra_id', 'tipe_waktu', 'jadwal_pelaksanaan', 
        'status', 'metode_pembayaran', 'durasi_kerja', 'total_biaya', 
        'komisi_zasha', 'keterangan_kerja', 'link_briefing', 'link_referensi', 'link_hasil_kerja', 'deadline', 'kuantitas'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $latest = self::latest('id')->first();
            $number = $latest ? (int) substr($latest->id, -4) + 1 : 1;
            $model->id = 'ZSH-ORD-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
}
