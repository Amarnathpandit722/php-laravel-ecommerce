<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Order extends Model
{
    //
    protected $fillable = [
        'order_number',
        'user_id',
        'address_id',
        'shipping_address',
        'subtotal',
        'shipping',
        'tax',
        'discount',
        'total',
        'currency',
        'status',
        'payment_method',
        'payment_status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'payment_meta',
        'paid_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'payment_meta'     => 'array',
        'paid_at'          => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalFormattedAttribute(): string
    {
        return '₹' . number_format($this->total / 100, 2);
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(now()->format('ymd')) . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
