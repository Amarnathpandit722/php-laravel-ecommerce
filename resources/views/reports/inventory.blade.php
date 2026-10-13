@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Inventory Report">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Products</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_products']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Units</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_stock']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Value</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($stats['total_value'], 0) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Low Stock</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['low_stock']) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock by Category</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($byCategory as $cat)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $cat->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $cat->products_count }} products · {{ $cat->total_stock ?? 0 }} units
                                </p>
                            </div>
                            <div class="w-48 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden ml-4">
                                @php
                                    $max = $byCategory->max('total_stock') ?: 1;
                                    $pct = ($cat->total_stock / $max) * 100;
                                @endphp
                                <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-sidebar-navbar>
@endsection