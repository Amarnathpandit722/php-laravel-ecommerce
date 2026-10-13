@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Orders">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">All Orders</h2>
                <div class="flex gap-2">
                    <a href="{{ route('orders.index') }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('orders.index') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        All
                    </a>
                    <a href="{{ route('orders.pending') }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('orders.pending') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        Pending
                    </a>
                    <a href="{{ route('orders.completed') }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request()->routeIs('orders.completed') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        Completed
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Order #</th>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 text-gray-900 dark:text-white">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $order->customer->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($order->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('orders.show', $order) }}"
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        </div>
    </x-sidebar-navbar>
@endsection