<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function current(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        return Cart::firstOrCreate(['session_id' => session()->getId()]);
    }

    public function add(Product $product, int $qty = 1): Cart
    {
        $cart = $this->current();
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);

        $newQty = ($item->exists ? $item->quantity : 0) + $qty;

        if ($newQty > $product->stock) {
            throw new \RuntimeException("Only {$product->stock} in stock.");
        }

        $item->quantity = $newQty;
        $item->save();

        return $cart->fresh(['items.product']);
    }

    public function update(Product $product, int $qty): Cart
    {
        $cart = $this->current();
        $item = $cart->items()->where('product_id', $product->id)->firstOrFail();

        if ($qty <= 0) {
            $item->delete();
        } else {
            if ($qty > $product->stock) {
                throw new \RuntimeException("Only {$product->stock} in stock.");
            }
            $item->update(['quantity' => $qty]);
        }

        return $cart->fresh(['items.product']);
    }

    public function remove(Product $product): Cart
    {
        $cart = $this->current();
        $cart->items()->where('product_id', $product->id)->delete();

        return $cart->fresh(['items.product']);
    }

    public function clear(): void
    {
        $this->current()->items()->delete();
    }

    public function mergeGuestCartIntoUser(int $userId): void
    {
        $guest = Cart::where('session_id', session()->getId())->first();
        if (!$guest) return;

        $user = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guest->items as $item) {
            $existing = $user->items()->where('product_id', $item->product_id)->first();
            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                $user->items()->create([
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                ]);
            }
        }

        $guest->delete();
    }
}
