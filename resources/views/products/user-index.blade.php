@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Shop">

        {{-- Hero banner --}}
        <div class="mb-6 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 rounded-2xl shadow-lg p-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-1/3 w-48 h-48 bg-white/5 rounded-full translate-y-1/2"></div>

            <div class="relative">
                <h1 class="text-3xl md:text-4xl font-bold">Discover great products ✨</h1>
                <p class="text-indigo-100 mt-2 max-w-lg">
                    Browse our curated collection. Search, filter by category, and find exactly what you need.
                </p>

                {{-- Search bar --}}
                <form action="{{ route('products.index') }}" method="GET" class="mt-6 max-w-xl relative">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search products by name..."
                           class="w-full px-5 py-3.5 pl-12 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white shadow-lg">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(request('search'))
                        <a href="{{ route('products.index', request()->except('search')) }}"
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Category pills + Sort --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('products.index', request()->except('category')) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition
                          {{ !request('category') ? 'bg-indigo-600 text-white shadow' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    All
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => $cat->id])) }}"
                       class="px-4 py-2 rounded-full text-sm font-medium transition flex items-center gap-1.5
                              {{ request('category') == $cat->id ? 'bg-indigo-600 text-white shadow' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                        {{ $cat->name }}
                        <span class="text-xs opacity-70">({{ $cat->products_count }})</span>
                    </a>
                @endforeach
            </div>

            <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2">
                @foreach(request()->except('sort', 'page') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <label class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">Sort:</label>
                <select name="sort" onchange="this.form.submit()"
                        class="px-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="latest"     @selected(request('sort', 'latest') === 'latest')>Newest</option>
                    <option value="price_asc"  @selected(request('sort') === 'price_asc')>Price: Low → High</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High → Low</option>
                    <option value="name"       @selected(request('sort') === 'name')>Name (A–Z)</option>
                </select>
            </form>
        </div>

        {{-- Results meta --}}
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $products->count() }}</span>
                of <span class="font-semibold text-gray-900 dark:text-white">{{ $products->total() }}</span> products
            </p>
        </div>

        {{-- Product Grid --}}
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <a href="{{ route('products.user-show', $product) }}"
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:-translate-y-1">

                        {{-- Image --}}
                        <div class="relative aspect-square bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <img src="{{ $product->image_url }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            {{-- Badges --}}
                            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                @if($product->featured)
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-yellow-400 text-yellow-900 rounded-full shadow">
                                        ⭐ Featured
                                    </span>
                                @endif
                                @if($product->discount_price)
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-red-500 text-white rounded-full shadow">
                                        -{{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                                    </span>
                                @endif
                                @if($product->stock <= 0)
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-gray-800/90 text-white rounded-full shadow">
                                        Out of stock
                                    </span>
                                @elseif($product->stock <= 5)
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-orange-500 text-white rounded-full shadow">
                                        Only {{ $product->stock }} left
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-4">
                            @if($product->category)
                                <p class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wide mb-1">
                                    {{ $product->category->name }}
                                </p>
                            @endif

                            <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                {{ $product->name }}
                            </h3>

                            @if($product->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ $product->description }}
                                </p>
                            @endif

                            {{-- Price --}}
                            <div class="flex items-center gap-2 mt-3">
                                @if($product->discount_price)
                                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($product->discount_price, 2) }}
                                    </span>
                                    <span class="text-sm text-gray-400 line-through">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                @else
                                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Rating placeholder --}}
                            <div class="flex items-center gap-1 mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-gray-400 ml-1">(24)</span>
                            </div>

                            {{-- CTA --}}
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($product->stock > 5)
                                        ✅ In stock
                                    @elseif($product->stock > 0)
                                        ⚠️ Low stock
                                    @else
                                        ❌ Out of stock
                                    @endif
                                </span>
                                <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                                    View
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            {{-- Empty state --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-16 text-center">
                <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    @if(request('search'))
                        No products match "{{ request('search') }}"
                    @else
                        No products available
                    @endif
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    Try a different search or browse another category.
                </p>
                @if(request()->hasAny(['search', 'category', 'sort']))
                    <a href="{{ route('products.index') }}"
                       class="inline-block mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                        Clear filters
                    </a>
                @endif
            </div>
        @endif

    </x-sidebar-navbar>
@endsection