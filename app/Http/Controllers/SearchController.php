<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q'));

        if (strlen($q) < 2) {
            return response()->json(['products' => [], 'orders' => [], 'customers' => []]);
        }

        $products = Product::where('name', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'url' => route('products.edit', $p),
            ]);

        $orders = Order::with('customer')
            ->where('id', 'like', "%{$q}%")
            ->orWhereHas('customer', fn($w) => $w->where('name', 'like', "%{$q}%"))
            ->limit(5)
            ->get()
            ->map(fn($o) => [
                'id' => $o->id,
                'customer' => $o->customer->name ?? 'Guest',
                'total' => $o->total,
                'url' => route('orders.show', $o),
            ]);

        $customers = Customer::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->limit(5)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'url' => route('customers.show', $c),
            ]);

        return response()->json(compact('products', 'orders', 'customers'));
    }

    //
}
