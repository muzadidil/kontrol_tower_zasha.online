<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    protected $fillable = [
        'order_type', 'order_id', 'raised_by_id', 'raised_by_type',
        'complaint_description', 'status', 'resolution',
        'refund_amount', 'admin_notes', 'resolved_by', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at'   => 'datetime',
        'refund_amount' => 'decimal:2',
    ];
}
