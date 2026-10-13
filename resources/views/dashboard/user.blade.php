@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="My Dashboard">

        {{-- Welcome banner --}}
        <div class="mb-6 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg shadow p-6 text-white">
            <h2 class="text-2xl font-bold">
                Hi, {{ auth()->user()->name }} 👋
            </h2>
            <p class="text-blue-100 mt-1">
                Here's a summary of your account.
            </p>
        </div>

        {{-- User KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">My Orders</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($myOrders) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">All time</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($myCompletedOrders) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Delivered successfully</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($myPendingOrders) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Awaiting processing</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-purple-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Spent</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($myTotalSpent, 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Lifetime value</p>
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Recent Orders</h3>
                <a href="{{ route('orders.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                    View all →
                </a>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($recentMyOrders as $order)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                Order #{{ $order->id }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $order->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($order->total, 2) }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($order->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">You haven't placed any orders yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </x-sidebar-navbar>
@endsection