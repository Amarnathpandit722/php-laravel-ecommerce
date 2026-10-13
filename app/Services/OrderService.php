<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private CartService $cartService) {}

    /**
     * Create a pending order from the user's cart + address.
     * Amounts are calculated server-side — never trust the client.
     */
    public function createFromCart(int $userId, int $addressId): Order
    {
        $cart = Cart::where('user_id', $userId)->with('items.product')->firstOrFail();

        if ($cart->items->isEmpty()) {
            throw new \RuntimeException('Your cart is empty.');
        }

        $address = Address::where('user_id', $userId)->findOrFail($addressId);

        if (!$address->is_serviceable) {
            throw new \RuntimeException('We do not deliver to this address.');
        }

        return DB::transaction(function () use ($cart, $address, $userId) {
            $subtotal = 0;
            $lines = [];

            foreach ($cart->items as $item) {
                $price = (int) round(($item->product->discount_price ?? $item->product->price) * 100);
                $lineTotal = $price * $item->quantity;
                $subtotal += $lineTotal;

                $lines[] = [
                    'product_id'    => $item->product->id,
                    'product_name'  => $item->product->name,
                    'product_image' => $item->product->image_url,
                    'unit_price'    => $price,
                    'quantity'      => $item->quantity,
                    'line_total'    => $lineTotal,
                ];
            }

            $shipping = $subtotal >= 50000 ? 0 : 5000; // free over ₹500
            $tax      = (int) round($subtotal * 0.18); // 18% GST example
            $total    = $subtotal + $shipping + $tax;

            $order = Order::create([
                'order_number'     => Order::generateOrderNumber(),
                'user_id'          => $userId,
                'address_id'       => $address->id,
                'shipping_address' => [
                    'label'       => $address->label,
                    'full_name'   => $address->full_name,
                    'phone'       => $address->phone,
                    'line1'       => $address->line1,
                    'line2'       => $address->line2,
                    'city'        => $address->city,
                    'state'       => $address->state,
                    'postal_code' => $address->postal_code,
                    'country'     => $address->country,
                ],
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax'      => $tax,
                'total'    => $total,
                'currency' => config('services.razorpay.currency', 'INR'),
                'status'   => 'pending',
            ]);

            $order->items()->createMany($lines);

            return $order->fresh('items');
        });
    }

    public function markPaid(Order $order, array $payment): void
    {
        $order->update([
            'status'               => 'paid',
            'payment_status'       => 'success',
            'payment_method'       => 'razorpay',
            'razorpay_order_id'    => $payment['razorpay_order_id']    ?? null,
            'razorpay_payment_id'  => $payment['razorpay_payment_id']  ?? null,
            'razorpay_signature'   => $payment['razorpay_signature']   ?? null,
            'payment_meta'         => $payment['meta'] ?? null,
            'paid_at'              => now(),
        ]);

        // Deduct stock
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        // Clear cart
        $this->cartService->clear();
    }

    public function markFailed(Order $order, ?string $reason = null): void
    {
        $order->update([
            'payment_status' => 'failed',
            'payment_meta'   => array_merge($order->payment_meta ?? [], ['error' => $reason]),
        ]);
    }
}
