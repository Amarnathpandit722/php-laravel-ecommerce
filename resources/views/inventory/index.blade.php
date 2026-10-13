@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Inventory">
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-gray-400">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">Total SKUs</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-green-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">In Stock</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ number_format($stats['in_stock']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">Low Stock</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ number_format($stats['low_stock']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-red-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">Out of Stock</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ number_format($stats['out_stock']) }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-indigo-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">Total Value</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($stats['total_value'], 0) }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('inventory.index') }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        All
                    </a>
                    <a href="{{ route('inventory.index', ['filter' => 'ok']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $filter === 'ok' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        ✅ In Stock
                    </a>
                    <a href="{{ route('inventory.index', ['filter' => 'low']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $filter === 'low' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        ⚠️ Low (1–5)
                    </a>
                    <a href="{{ route('inventory.index', ['filter' => 'out']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ $filter === 'out' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        ❌ Out of Stock
                    </a>
                </div>

                <form action="{{ route('inventory.index') }}" method="GET" class="flex gap-2">
                    @if($filter !== 'all')
                        <input type="hidden" name="filter" value="{{ $filter }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                           class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <button class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                        Search
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">SKU</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3 text-right">Price</th>
                            <th class="px-6 py-3 text-right">Stock</th>
                            <th class="px-6 py-3 text-right">Value</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product->image_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($product->name) }}"
                                             class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                                             alt="">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-400">
                                    {{ $product->sku ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $product->category->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-900 dark:text-white">
                                    ${{ number_format($product->price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">
                                    {{ $product->stock }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-900 dark:text-white">
                                    ${{ number_format($product->stock * $product->price, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($product->stock <= 0)
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                            Out of stock
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">
                                            Low ({{ $product->stock }})
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                            In stock
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    No products match your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </x-sidebar-navbar>
@endsection