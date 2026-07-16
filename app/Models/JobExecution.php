<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobExecution extends Model
{
    protected $fillable = [
        'job_name',
        'order_id',
        'status',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];
}
