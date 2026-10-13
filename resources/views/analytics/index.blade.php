@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Analytics">

        {{-- =============== HEADER + RANGE FILTER =============== --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics Overview</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Live data from your store · Updated {{ now()->format('M d, Y H:i') }}
                </p>
            </div>

            <form method="GET" class="flex items-center gap-2">
                <label class="text-sm text-gray-500 dark:text-gray-400">Range:</label>
                <select name="range" onchange="this.form.submit()"
                        class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="7"   @selected($range == '7')>Last 7 days</option>
                    <option value="30"  @selected($range == '30')>Last 30 days</option>
                    <option value="90"  @selected($range == '90')>Last 90 days</option>
                    <option value="365" @selected($range == '365')>Last year</option>
                    <option value="all" @selected($range == 'all')>All time</option>
                </select>
            </form>
        </div>

        {{-- =============== KPI CARDS =============== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

            @php
                $kpis = [
                    [
                        'label' => 'Total Revenue',
                        'value' => '$' . number_format($totalRevenue, 2),
                        'change'=> $revenueChange,
                        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' => 'indigo',
                    ],
                    [
                        'label' => 'Total Orders',
                        'value' => number_format($totalOrders),
                        'change'=> $ordersChange,
                        'icon'  => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                        'color' => 'blue',
                    ],
                    [
                        'label' => 'New Customers',
                        'value' => number_format($totalCustomers),
                        'change'=> $customersChange,
                        'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                        'color' => 'green',
                    ],
                    [
                        'label' => 'Avg Order Value',
                        'value' => '$' . number_format($avgOrderValue, 2),
                        'change'=> $aovChange,
                        'icon'  => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                        'color' => 'purple',
                    ],
                ];
            @endphp

            @foreach ($kpis as $kpi)
                @php
                    $isUp = $kpi['change'] >= 0;
                    $colorMap = [
                        'indigo' => 'bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400',
                        'blue'   => 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400',
                        'green'  => 'bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400',
                        'purple' => 'bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400',
                    ];
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-{{ $kpi['color'] }}-500">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $kpi['label'] }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $kpi['value'] }}</p>

                            <div class="flex items-center gap-1 mt-2">
                                @if ($isUp)
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    <span class="text-xs font-medium text-green-600 dark:text-green-400">
                                        +{{ $kpi['change'] }}%
                                    </span>
                                @else
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    <span class="text-xs font-medium text-red-600 dark:text-red-400">
                                        {{ $kpi['change'] }}%
                                    </span>
                                @endif
                                <span class="text-xs text-gray-400 dark:text-gray-500">vs prev period</span>
                            </div>
                        </div>

                        <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $colorMap[$kpi['color']] }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kpi['icon'] }}"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- =============== ROW 1: Revenue line + Orders status doughnut =============== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- Revenue over time --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Over Time</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Daily completed orders</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <span class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                            <span class="w-3 h-3 rounded-full bg-indigo-500"></span> Revenue
                        </span>
                        <span class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span> Orders
                        </span>
                    </div>
                </div>
                <div class="h-72">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- Orders by status --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Orders by Status</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Distribution in range</p>
                <div class="h-56">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- =============== ROW 2: Top products + Category pie =============== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            {{-- Top products bar --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Top 5 Products</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">By revenue in range</p>
                <div class="h-72">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>

            {{-- Revenue by category --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Revenue by Category</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Share of total revenue</p>
                <div class="h-72">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        {{-- =============== ROW 3: Customer stats + Recent orders + Low stock =============== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- New vs Returning --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Customer Mix</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">New vs returning</p>
                <div class="h-56">
                    <canvas id="customerChart"></canvas>
                </div>
            </div>

            {{-- Recent orders --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h3>
                    <a href="{{ route('orders.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        View all →
                    </a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-72 overflow-y-auto">
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

            {{-- Low stock --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">⚠️ Low Stock</h3>
                    <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        Manage →
                    </a>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-72 overflow-y-auto">
                    @forelse ($lowStock as $product)
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

        {{-- =============== Chart.js via CDN =============== --}}
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
            const tickColor = isDark ? '#9ca3af' : '#6b7280';

            Chart.defaults.font.family = 'ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto';
            Chart.defaults.color = tickColor;

            // ---------- Shared tooltip styling ----------
            const tooltipStyle = {
                backgroundColor: isDark ? '#1f2937' : '#ffffff',
                titleColor: isDark ? '#f3f4f6' : '#111827',
                bodyColor: isDark ? '#d1d5db' : '#374151',
                borderColor: isDark ? '#374151' : '#e5e7eb',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 8,
                displayColors: true,
            };

            // ---------- 1. Revenue + Orders line chart ----------
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
            revenueGradient.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
            revenueGradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: @json($chartRevenue),
                            borderColor: '#6366f1',
                            backgroundColor: revenueGradient,
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#6366f1',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Orders',
                            data: @json($chartOrders),
                            borderColor: '#10b981',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.35,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            yAxisID: 'y1',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipStyle,
                            callbacks: {
                                label: (ctx) => {
                                    if (ctx.dataset.label === 'Revenue')
                                        return ' Revenue: $' + ctx.parsed.y.toLocaleString();
                                    return ' Orders: ' + ctx.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 }
                        },
                        y: {
                            position: 'left',
                            grid: { color: gridColor },
                            ticks: {
                                callback: v => '$' + v.toLocaleString()
                            }
                        },
                        y1: {
                            position: 'right',
                            grid: { display: false },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });

            // ---------- 2. Orders by status doughnut ----------
            const statusData = @json($ordersByStatus);
            const statusColors = {
                completed: '#10b981',
                pending:   '#f59e0b',
                cancelled: '#ef4444',
            };
            const statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
            const statusValues = Object.values(statusData);
            const statusBg = Object.keys(statusData).map(s => statusColors[s] || '#6366f1');

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusBg,
                        borderWidth: 0,
                        hoverOffset: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 14,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                            }
                        },
                        tooltip: tooltipStyle,
                    },
                }
            });

            // ---------- 3. Top products bar ----------
            const topProducts = @json($topProducts);
            const tpLabels = topProducts.map(p => p.name);
            const tpRevenue = topProducts.map(p => parseFloat(p.revenue));
            const tpUnits = topProducts.map(p => parseInt(p.units_sold));

            new Chart(document.getElementById('topProductsChart'), {
                type: 'bar',
                data: {
                    labels: tpLabels,
                    datasets: [{
                        label: 'Revenue',
                        data: tpRevenue,
                        backgroundColor: 'rgba(99, 102, 241, 0.85)',
                        hoverBackgroundColor: '#6366f1',
                        borderRadius: 6,
                        barThickness: 26,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipStyle,
                            callbacks: {
                                label: (ctx) => {
                                    const i = ctx.dataIndex;
                                    return [
                                        ' Revenue: $' + tpRevenue[i].toLocaleString(),
                                        ' Units sold: ' + tpUnits[i],
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: { callback: v => '$' + v.toLocaleString() }
                        },
                        y: { grid: { display: false } }
                    }
                }
            });

            // ---------- 4. Revenue by category pie ----------
            const categoryData = @json($revenueByCategory);
            const catLabels = categoryData.map(c => c.name);
            const catRevenue = categoryData.map(c => parseFloat(c.revenue));
            const pieColors = [
                '#6366f1', '#10b981', '#f59e0b', '#ef4444',
                '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16',
            ];

            new Chart(document.getElementById('categoryChart'), {
                type: 'pie',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catRevenue,
                        backgroundColor: pieColors,
                        borderWidth: 0,
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                            }
                        },
                        tooltip: {
                            ...tooltipStyle,
                            callbacks: {
                                label: (ctx) => {
                                    const total = catRevenue.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                    return ' ' + ctx.label + ': $' + ctx.parsed.toLocaleString() + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // ---------- 5. New vs Returning customers ----------
            new Chart(document.getElementById('customerChart'), {
                type: 'bar',
                data: {
                    labels: ['New', 'Returning'],
                    datasets: [{
                        data: [{{ $newCustomers }}, {{ $returningCustomers }}],
                        backgroundColor: ['#6366f1', '#10b981'],
                        borderRadius: 8,
                        barThickness: 48,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: tooltipStyle,
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { color: gridColor },
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        })();
        </script>
        @endpush

    </x-sidebar-navbar>
@endsection