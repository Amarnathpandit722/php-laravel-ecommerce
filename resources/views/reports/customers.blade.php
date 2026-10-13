@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Customer Report">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Customers</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New in Period</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['new_period']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border-l-4 border-purple-500">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg. Customer Value</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($stats['avg_spent'], 2) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Customers</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3 text-right">Orders</th>
                            <th class="px-6 py-3 text-right">Total Spent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $customer->name }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $customer->email }}</td>
                                <td class="px-6 py-4 text-right text-gray-900 dark:text-white">{{ $customer->orders_count }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                    ${{ number_format($customer->total_spent ?? 0, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No customers yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">{{ $customers->links() }}</div>
            @endif
        </div>
    </x-sidebar-navbar>
@endsection