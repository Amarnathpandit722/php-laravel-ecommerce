<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\CartItem;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            $price = $item->product->discount_price ?? $item->product->price;
            return $price * $item->quantity;
        });
    }

    public function getCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
