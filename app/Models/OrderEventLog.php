<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderEventLog extends Model
{
    protected $fillable = [
        'order_id',
        'event_name',
        'stage',
        'payload',
        'timestamp',
    ];

    protected $casts = [
        'payload' => 'array',
        'timestamp' => 'datetime',
    ];
}
