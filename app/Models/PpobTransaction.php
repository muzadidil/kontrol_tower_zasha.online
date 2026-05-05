<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpobTransaction extends Model
{
    protected $fillable = [
        'id', 'user_id', 'user_type', 'sku', 'target_number', 'price', 'selling_price', 'status', 'ref_id', 'sn'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
