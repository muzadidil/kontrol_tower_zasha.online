<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_type', 'user_id', 'device_name', 'device_id',
        'token', 'is_active', 'last_active_at',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'last_active_at' => 'datetime',
    ];
}
