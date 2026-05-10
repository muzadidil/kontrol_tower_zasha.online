<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderItem extends Model
{
    protected $fillable = ['service_order_id', 'tipe', 'nama_item', 'harga', 'catatan'];

    protected $casts = ['harga' => 'decimal:2'];

    public function serviceOrder(): BelongsTo { return $this->belongsTo(ServiceOrder::class); }
}
