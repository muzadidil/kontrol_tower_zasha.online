<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupRequest extends Model
{
    protected $fillable = [
        'user_type', 'user_id', 'jumlah', 'tokopay_ref',
        'status', 'tokopay_response', 'paid_at',
    ];

    protected $casts = [
        'jumlah'           => 'decimal:2',
        'tokopay_response' => 'array',
        'paid_at'          => 'datetime',
    ];
}
