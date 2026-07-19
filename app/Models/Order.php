<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'recipient_name',
        'phone',
        'address',
        'city',
        'postal_code',
        'notes',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_method',
        'payment_proof',
        'paid_at',
        'processing_stage',
        'pipeline_status',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
