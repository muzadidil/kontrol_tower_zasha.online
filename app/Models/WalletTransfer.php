<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransfer extends Model
{
    protected $fillable = ['sender_mitra_id', 'receiver_mitra_id', 'amount', 'description'];

    public function sender()
    {
        return $this->belongsTo(Mitra::class, 'sender_mitra_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Mitra::class, 'receiver_mitra_id');
    }
}
