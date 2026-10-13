@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Admin Dashboard">

        {{-- Welcome banner --}}
        <div class="mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">
                        Welcome back, {{ auth()->user()->name }} 👑
                    </h2>
                    <p class="text-indigo-100 mt-1">
                        Here's your store overview for today.
                    </p>
                </div>
                <div class="hidden md:block text-right">
                    <p class="text-sm text-indigo-100">{{ now()->format('l, F j, Y') }}</p>
                    <p class="text-xs text-indigo-200 mt-1">Administrator</p>
                </div>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            ${{ number_format($totalRevenue, 2) }}
                        </p>
                        <div class="flex items-center gap-1 mt-2">
                            @if ($revenueGrowth >= 0)
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                </svg>
                                <span class="text-xs font-medium text-green-600 dark:text-green-400">
                                    +{{ $revenueGrowth }}%
                                </span>
                            @else
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                                <span class="text-xs font-medium text-red-600 dark:text-red-400">
                                    {{ $revenueGrowth }}%
                                </span>
                            @endif
                            <span class="text-xs text-gray-400">vs last month</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalOrders) }}</p>
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-2">
                            {{ $pendingOrders }} pending
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Customers</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalCustomers) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Total registered</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Products</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalProducts) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">In catalog</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue chart + Top products --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue — Last 7 Days</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Completed orders only</p>
                    </div>
                    <a href="{{ route('analytics') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        View full analytics →
                    </a>
                </div>
                <div class="h-64">
                    <canvas id="dashboardRevenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Top Products</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Best sellers</p>

                <div class="space-y-3">
                    @forelse ($topProducts as $i => $product)
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ $i + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $product->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $product->units_sold }} sold
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No sales yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent orders + Low stock --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h3>
                    <a href="{{ route('orders.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        View all →
                    </a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($recentOrders as $order)
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($order->customer->name ?? 'N', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $order->customer->name ?? 'Guest' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        #{{ $order->id }} · {{ $order->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
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
                        <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">No orders yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">⚠️ Low Stock Alerts</h3>
                    <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        Manage →
                    </a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($lowStockProducts as $product)
                        <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $product->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </p>
                            </div>
                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full flex-shrink-0
                                {{ $product->stock <= 0
                                    ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' }}">
                                {{ $product->stock }} left
                            </span>
                        </div>
                    @empty
                        <p class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            ✅ All products well stocked.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
            const tickColor = isDark ? '#9ca3af' : '#6b7280';

            Chart.defaults.color = tickColor;

            const ctx = document.getElementById('dashboardRevenueChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($chartRevenue),
                        borderColor: '#6366f1',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1f2937' : '#ffffff',
                            titleColor: isDark ? '#f3f4f6' : '#111827',
                            bodyColor: isDark ? '#d1d5db' : '#374151',
                            borderColor: isDark ? '#374151' : '#e5e7eb',
                            borderWidth: 1,
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: ctx => ' $' + ctx.parsed.y.toLocaleString()
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { color: gridColor },
                            ticks: { callback: v => '$' + v.toLocaleString() }
                        }
                    }
                }
            });
        })();
        </script>
        @endpush

    </x-sidebar-navbar>
@endsection