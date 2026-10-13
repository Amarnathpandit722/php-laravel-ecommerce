@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Sales Report">
        <form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}"
                       class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}"
                       class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                Apply
            </button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Revenue</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($totals['revenue'], 2) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Orders</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totals['orders']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-purple-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Order Value</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($totals['avg'], 2) }}</p>
            </div>
        </div>

        {{-- Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue by Day</h3>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Orders table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">#{{ $order->id }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $order->customer->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($order->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No orders in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">{{ $orders->links() }}</div>
            @endif
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark');
            const ctx = document.getElementById('salesChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($byDay->pluck('date')),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($byDay->pluck('total')),
                        borderColor: '#10b981',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: { label: ctx => ' $' + ctx.parsed.y.toLocaleString() }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { color: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)' },
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