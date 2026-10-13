<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        //     $orders = Order::with('customer')->latest()->paginate(10);
        //     return view('orders.index', compact('orders'));

        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function pending()
    {
        $orders = Order::with('customer')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
        return view('orders.pending', compact('orders'));
    }

    public function completed()
    {
        $orders = Order::with('customer')
            ->where('status', 'completed')
            ->latest()
            ->paginate(10);
        return view('orders.completed', compact('orders'));
    }

    public function show(Order $order)
    {
        // $order->load('customer', 'items.product');
        // return view('orders.show', compact('order'));

        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items', 'address');

        return view('orders.show', compact('order'));
    }
}
