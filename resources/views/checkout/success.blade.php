@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Payment Successful">
        <div class="max-w-2xl mx-auto text-center bg-white dark:bg-gray-800 rounded-2xl shadow p-10">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order placed successfully!</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                Your order <strong>{{ $order->order_number }}</strong> has been confirmed.
            </p>

            <div class="mt-6 text-left bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Payment ID</span><span class="font-mono">{{ $order->razorpay_payment_id }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Amount paid</span><span class="font-semibold">{{ $order->total_formatted }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="capitalize">{{ $order->status }}</span></div>
            </div>

            <div class="mt-6 flex gap-3 justify-center">
                <a href="{{ route('orders.show', $order) }}"
                   class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                    View order
                </a>
                <a href="{{ route('dashboard') }}"
                   class="px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium">
                    Continue shopping
                </a>
            </div>
        </div>
    </x-sidebar-navbar>
@endsection