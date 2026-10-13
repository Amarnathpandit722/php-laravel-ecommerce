@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Dashboard">

        @if (session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Welcome, {{ auth()->user()->name }} 👋
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    You are logged in as <strong>{{ auth()->user()->email }}</strong>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalRevenue) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalOrders) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Customers</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCustomers) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Conversion</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $conversionRate }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                <p class="text-gray-500 dark:text-gray-400">No recent activity.</p>
            </div>
        </div>

    </x-sidebar-navbar>
@endsection