@extends('layouts.app')

@section('content')
<x-sidebar-navbar title="Products">
    @if (session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Products</h2>
            <a href="{{ route('products.create') }}"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                + Add Product
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Stock</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($products as $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $product->category->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $product->stock }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                             @if(auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product) }}"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this product?')"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400">Delete</button>
                            </form>
                              @else
 <a href="{{ route('products.show', $product) }}"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">View</a>
                              @endif    
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No products found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            {{ $products->links() }}
        </div>
    </div>
</x-sidebar-navbar>
@endsection