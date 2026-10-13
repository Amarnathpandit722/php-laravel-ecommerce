@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Customers">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Customers</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Orders</th>
                            <th class="px-6 py-3">Joined</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $customer->name }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $customer->email }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $customer->orders_count }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $customer->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 dark:text-indigo-400">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No customers.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">{{ $customers->links() }}</div>
        </div>
    </x-sidebar-navbar>
@endsection