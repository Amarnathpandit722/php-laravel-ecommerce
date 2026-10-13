@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Order #{{ $order->id }}">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order #{{ $order->id }}</h2>
                <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400">← Back to orders</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $order->customer->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($order->status) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                    <p class="font-medium text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</p>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Items</h3>
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2">Product</th>
                        <th class="px-4 py-2">Qty</th>
                        <th class="px-4 py-2">Price</th>
                        <th class="px-4 py-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($order->items ?? [] as $item)
                        <tr>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $item->product->name ?? '—' }}</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${{ number_format($item->price, 2) }}</td>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No items.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-sidebar-navbar>
@endsection