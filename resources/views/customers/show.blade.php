@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="{{ $customer->name }}">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h2>
                <a href="{{ route('customers.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400">← Back</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $customer->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Joined</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $customer->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Order History</h3>
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2">Order #</th>
                        <th class="px-4 py-2">Total</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($customer->orders as $order)
                        <tr>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">#{{ $order->id }}</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">${{ number_format($order->total, 2) }}</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ ucfirst($order->status) }}</td>
                            <td class="px-4 py-2 text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-sidebar-navbar>
@endsection