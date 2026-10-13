<?php

namespace App\Http\Controllers;


use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        $cart = $this->cart->current()->load('items.product');
        return view('cart.index', compact('cart'));
    }

    /** POST /cart/add  { product_id, quantity } */
    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        try {
            $product = Product::findOrFail($data['product_id']);
            $cart = $this->cart->add($product, $data['quantity'] ?? 1);

            return $this->cartResponse($cart, 'Added to cart');
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** PATCH /cart/update  { product_id, quantity } */
    public function update(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        try {
            $product = Product::findOrFail($data['product_id']);
            $cart = $this->cart->update($product, $data['quantity']);

            return $this->cartResponse($cart, 'Cart updated');
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /** DELETE /cart/remove  { product_id } */
    public function remove(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $cart = $this->cart->remove(Product::findOrFail($data['product_id']));

        return $this->cartResponse($cart, 'Removed from cart');
    }

    /** GET /cart/count  — for navbar badge polling */
    public function count()
    {
        $cart = $this->cart->current()->load('items');

        return response()->json([
            'ok'    => true,
            'count' => $cart->count,
        ]);
    }

    private function cartResponse($cart, string $message)
    {
        $cart->load('items.product');

        if (request()->expectsJson()) {
            return response()->json([
                'ok'    => true,
                'message' => $message,
                'count' => $cart->count,
                'total' => $cart->total,
                'items' => $cart->items->map(fn($i) => [
                    'product_id' => $i->product_id,
                    'name'       => $i->product->name,
                    'price'      => $i->product->discount_price ?? $i->product->price,
                    'quantity'   => $i->quantity,
                    'line_total' => ($i->product->discount_price ?? $i->product->price) * $i->quantity,
                ]),
            ]);
        }

        return back()->with('success', $message);
    }
}
