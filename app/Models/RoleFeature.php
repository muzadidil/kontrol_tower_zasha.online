<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleFeature extends Model
{
    protected $fillable = ['role_id', 'feature_key'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
