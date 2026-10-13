<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;

class ActivityController extends Controller
{
    public function index()
    {
        // Merge recent events from different sources into one feed
        $activities = collect();

        // Recent orders
        foreach (Order::with('user')->latest()->limit(15)->get() as $o) {
            $activities->push([
                'icon'       => '🛒',
                'color'      => 'green',
                'title'      => "New order #{$o->id}",
                'description' => ($o->user->name ?? 'Guest') . " placed an order for \${$o->total}",
                'time'       => $o->created_at,
                'url'        => route('orders.show', $o),
            ]);
        }

        // Recent products
        foreach (Product::latest()->limit(10)->get() as $p) {
            $activities->push([
                'icon'       => '📦',
                'color'      => 'indigo',
                'title'      => 'Product added',
                'description' => $p->name . " was added to catalog",
                'time'       => $p->created_at,
                'url'        => route('products.edit', $p),
            ]);
        }

        // Recent customers
        foreach (Customer::latest()->limit(10)->get() as $c) {
            $activities->push([
                'icon'       => '👤',
                'color'      => 'blue',
                'title'      => 'New customer',
                'description' => $c->name . " ({$c->email}) registered",
                'time'       => $c->created_at,
                'url'        => route('customers.show', $c),
            ]);
        }

        $activities = $activities->sortByDesc('time')->take(40)->values();

        return view('activity.index', compact('activities'));
    }
}
