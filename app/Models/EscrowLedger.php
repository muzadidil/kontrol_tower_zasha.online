<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscrowLedger extends Model
{
    protected $fillable = ['wfh_order_id', 'type', 'amount', 'keterangan', 'triggered_by'];

    protected $casts = ['amount' => 'decimal:2'];

    public function wfhOrder(): BelongsTo { return $this->belongsTo(WfhOrder::class); }
}
