<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'nama_barang', 'lokasi_beli', 'harga_perkiraan', 'harga_asli', 'status_beli'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
