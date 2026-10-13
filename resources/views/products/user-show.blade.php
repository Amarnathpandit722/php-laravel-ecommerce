@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="{{ $product->name }}">

        {{-- Back --}}
        <div class="mb-4">
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to shop
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- Image --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow overflow-hidden">
                <div class="aspect-square bg-gray-100 dark:bg-gray-700">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                </div>
            </div>

            {{-- Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-8">
                @if($product->category)
                    <a href="{{ route('products.index', ['category' => $product->category->id]) }}"
                       class="inline-block px-3 py-1 text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 rounded-full mb-3">
                        {{ $product->category->name }}
                    </a>
                @endif

                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h1>

                {{-- Rating --}}
                <div class="flex items-center gap-1 mt-3">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">4.0 · 24 reviews</span>
                </div>

                {{-- Price --}}
                <div class="flex items-baseline gap-3 mt-6">
                    @if($product->discount_price)
                        <span class="text-4xl font-bold text-gray-900 dark:text-white">
                            ${{ number_format($product->discount_price, 2) }}
                        </span>
                        <span class="text-xl text-gray-400 line-through">
                            ${{ number_format($product->price, 2) }}
                        </span>
                        <span class="px-2 py-1 text-xs font-bold bg-red-500 text-white rounded">
                            Save ${{ number_format($product->price - $product->discount_price, 2) }}
                        </span>
                    @else
                        <span class="text-4xl font-bold text-gray-900 dark:text-white">
                            ${{ number_format($product->price, 2) }}
                        </span>
                    @endif
                </div>

                {{-- Stock --}}
                <div class="mt-4">
                    @if($product->stock > 5)
                        <span class="inline-flex items-center gap-2 text-sm text-green-600 dark:text-green-400 font-medium">
                            ✅ In stock — ready to ship
                        </span>
                    @elseif($product->stock > 0)
                        <span class="inline-flex items-center gap-2 text-sm text-orange-600 dark:text-orange-400 font-medium">
                            ⚠️ Only {{ $product->stock }} left in stock
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 text-sm text-red-600 dark:text-red-400 font-medium">
                            ❌ Currently out of stock
                        </span>
                    @endif
                </div>

                {{-- Description --}}
                @if($product->description)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Description</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ $product->description }}
                        </p>
                    </div>
                @endif

                {{-- Tags --}}
                @if(!empty($product->tags))
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->tags as $tag)
                                <span class="px-3 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CTA --}}
                {{-- <div class="mt-8 flex gap-3">
                    <button type="button"
                            @disabled($product->stock <= 0)
                            onclick="window.toast && window.toast('Added to cart — demo only', 'success')"
                            class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition">
                        Add to Cart
                    </button>
                    <button type="button"
                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        ♡ Save
                    </button>
                </div> --}}


                <div
    x-data="addToCart({
        productId: {{ $product->id }},
        inStock: {{ $product->stock > 0 ? 'true' : 'false' }},
        addUrl: '{{ route('cart.add') }}'
    })"
    class="mt-8 flex gap-3"
>
    <button
        type="button"
        @click="add()"
        :disabled="loading || !inStock"
        class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition flex items-center justify-center gap-2"
    >
        <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/>
            <path fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
        </svg>
        <span x-text="loading ? 'Adding…' : (inStock ? 'Add to Cart' : 'Out of stock')"></span>
    </button>

    <button type="button"
            class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        ♡ Save
    </button>
</div>
            </div>
        </div>


{{-- ============================================================= --}}
{{-- PRODUCT SPECIFICATIONS (Amazon-style accordion)               --}}
{{-- ============================================================= --}}
@if(!empty($specsBySection))
@php
    /**
     * Build a stable, JS-safe key map for each section.
     * We use the raw section name as the key so we can use it directly
     * inside the Alpine `open` object — no md5 needed.
     *
     * Alpine state: open = { "Processor": true, "Display": false, ... }
     */
    $sectionKeys = array_keys($specsBySection);

    // Prefill state: first section open by default (Amazon-like)
    $initialOpen = [];
    foreach ($sectionKeys as $i => $key) {
        $initialOpen[$key] = $i === 0; // first one true, rest false
    }
@endphp

<div class="mt-8 bg-white dark:bg-gray-800 rounded-2xl shadow p-6"
     x-data="specsAccordion(@js($initialOpen))">

    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
        <span class="w-1 h-6 bg-amber-500 rounded"></span>
        {{ $product->name }} Information
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($specsBySection as $section => $rows)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

                {{-- Section header --}}
                <button type="button"
                        @click="toggle(@js($section))"
                        class="w-full flex items-center justify-between px-4 py-3
                               bg-gray-50 dark:bg-gray-700/50
                               hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <span class="font-medium text-gray-900 dark:text-white">
                        {{ $section }}
                    </span>
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                         :class="isOpen(@js($section)) ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Section body --}}
                <div x-show="isOpen(@js($section))"
                     x-collapse
                     x-cloak
                     class="bg-white dark:bg-gray-800">
                    <table class="w-full text-sm">
                        <tbody>
                            @forelse($rows as $row)
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="px-4 py-2.5 font-medium text-gray-600 dark:text-gray-300 w-2/5 align-top">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-gray-800 dark:text-gray-200">
                                        {{ $row['value'] ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td colspan="2" class="px-4 py-4 text-center text-xs text-gray-400">
                                        No specifications in this section.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

        {{-- ============================================================= --}}
{{-- REVIEWS SECTION                                                --}}
{{-- ============================================================= --}}
<div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ---------- LEFT: Summary + Rating breakdown ---------- --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Customer Reviews</h3>

            <div class="flex items-center gap-4 mb-6">
                <div class="text-center">
                    <p class="text-5xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($product->average_rating, 1) }}
                    </p>
                    <div class="flex items-center gap-0.5 mt-1 justify-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $product->reviews_count }} {{ Str::plural('review', $product->reviews_count) }}
                    </p>
                </div>
            </div>

            {{-- Rating breakdown bars --}}
            @php $breakdown = $product->rating_breakdown; $total = array_sum($breakdown) ?: 1; @endphp
            <div class="space-y-2">
                @for($star = 5; $star >= 1; $star--)
                    @php $count = $breakdown[$star] ?? 0; $pct = ($count / $total) * 100; @endphp
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-8 text-gray-600 dark:text-gray-400">{{ $star }} ★</span>
                        <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400 rounded-full transition-all"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-8 text-right text-gray-500 dark:text-gray-400">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Write review CTA --}}
        @if($canReview)
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow p-6 text-white">
                <h4 class="font-semibold mb-1">Bought this product?</h4>
                <p class="text-sm text-indigo-100 mb-4">Share your experience and help other shoppers.</p>
                <button type="button"
                        onclick="document.getElementById('reviewForm').scrollIntoView({behavior:'smooth'}); document.getElementById('reviewForm').classList.remove('hidden');"
                        class="w-full px-4 py-2.5 bg-white text-indigo-600 rounded-lg font-medium hover:bg-indigo-50 transition">
                    ✍️ Write a review
                </button>
            </div>
        @elseif($userReview)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-4">
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    You reviewed this product —
                    <span class="font-semibold">
                        @if($userReview->status === 'pending') ⏳ Pending approval
                        @elseif($userReview->status === 'approved') ✅ Published
                        @else ❌ Rejected
                        @endif
                    </span>
                </p>
                <a href="#yourReview"
                   class="text-xs text-blue-600 dark:text-blue-400 underline mt-1 inline-block">
                    Jump to your review
                </a>
            </div>
        @endif
    </div>

    {{-- ---------- RIGHT: Review list + write form ---------- --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- WRITE FORM (hidden until toggled) --}}
        @if($canReview)
            <div id="reviewForm" class="hidden bg-white dark:bg-gray-800 rounded-2xl shadow p-6 border-2 border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Write your review</h3>
                    <button type="button" onclick="document.getElementById('reviewForm').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-600">
                        ✕
                    </button>
                </div>

                @if(session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('reviews.store', $product) }}" method="POST"
                      enctype="multipart/form-data" x-data="reviewFormComponent">
                    @csrf

                    {{-- Rating --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Your rating <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-1">
                            <template x-for="i in 5" :key="i">
                                <button type="button"
                                        @click="rating = i"
                                        @mouseenter="hoverRating = i"
                                        @mouseleave="hoverRating = 0"
                                        class="focus:outline-none transition-transform hover:scale-110">
                                    <svg class="w-10 h-10 transition-colors"
                                         :class="(hoverRating || rating) >= i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </template>
                            <span class="ml-3 text-sm text-gray-500 dark:text-gray-400"
                                  x-text="rating ? ['','Terrible','Poor','OK','Good','Excellent'][rating] : 'Select a rating'"></span>
                        </div>
                        <input type="hidden" name="rating" :value="rating" required>
                        @error('rating')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Title <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <input type="text" name="title" maxlength="120"
                               value="{{ old('title') }}"
                               placeholder="Summarize your experience in a few words"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    {{-- Body --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Your review <span class="text-red-500">*</span>
                            </label>
                            <span class="text-xs text-gray-400">
                                <span x-text="body.length"></span> / 2000
                            </span>
                        </div>
                        <textarea name="body" rows="5" maxlength="2000" required
                                  x-model="body"
                                  placeholder="What did you like or dislike? How was the quality?"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 resize-none">{{ old('body') }}</textarea>
                    </div>

                    {{-- Image upload --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Add photos <span class="text-gray-400 font-normal">(up to 5, 3MB each)</span>
                        </label>

                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-3" id="imagePreviewGrid">
                            {{-- Live previews inserted by JS --}}
                        </div>

                        <label for="reviewImages"
                               class="mt-3 inline-flex items-center gap-2 px-4 py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Add photos</span>
                            <input type="file" name="images[]" id="reviewImages"
                                   accept="image/*" multiple class="hidden">
                        </label>
                        @error('images.*')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                :disabled="!rating || body.length < 10"
                                class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition">
                            Submit review
                        </button>
                        <button type="button"
                                onclick="document.getElementById('reviewForm').classList.add('hidden')"
                                class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- USER'S OWN REVIEW (if any) --}}
        @if($userReview)
            <div id="yourReview" class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 border-2 border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                        Your review
                    </span>
                    <span class="px-2 py-0.5 text-xs rounded-full
                        @if($userReview->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                        @elseif($userReview->status === 'approved') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                        @else bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 @endif">
                        {{ ucfirst($userReview->status) }}
                    </span>
                </div>

                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $userReview->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>

                @if($userReview->title)
                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $userReview->title }}</h4>
                @endif
                <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">{{ $userReview->body }}</p>

                @if($userReview->images)
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($userReview->image_urls as $url)
                            <img src="{{ $url }}" class="w-16 h-16 rounded-lg object-cover border border-gray-200 dark:border-gray-600 cursor-pointer"
                                 onclick="window.open('{{ $url }}', '_blank')">
                        @endforeach
                    </div>
                @endif

                <div class="flex gap-3 mt-4">
                    <form action="{{ route('reviews.destroy', $userReview) }}" method="POST"
                          onsubmit="return confirm('Delete your review?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- APPROVED REVIEWS LIST --}}
        <div class="space-y-4">
            @forelse($product->approvedReviews as $review)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=6366f1&color=fff"
                                 class="w-10 h-10 rounded-full" alt="">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</p>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    @if($review->is_verified_purchase)
                                        <span class="text-xs text-green-600 dark:text-green-400 flex items-center gap-1">
                                            ✓ Verified purchase
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                    </div>

                    @if($review->title)
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $review->title }}</h4>
                    @endif
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $review->body }}</p>

                    @if($review->images)
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach($review->image_urls as $url)
                                <img src="{{ $url }}"
                                     class="w-20 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600 cursor-pointer hover:scale-105 transition"
                                     onclick="window.open('{{ $url }}', '_blank')">
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <form action="{{ route('reviews.helpful', $review) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                </svg>
                                Helpful ({{ $review->helpful_count }})
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No reviews yet. Be the first to review this product!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reviewFormComponent', () => ({
            rating: 0,
            hoverRating: 0,
            body: '',
        }));
    });

    // ---------- Image preview handler ----------
    (function () {
        const input = document.getElementById('reviewImages');
        const grid  = document.getElementById('imagePreviewGrid');
        if (!input || !grid) return;

        const maxFiles = 5;

        input.addEventListener('change', (e) => {
            grid.innerHTML = '';
            const files = Array.from(e.target.files).slice(0, maxFiles);

            files.forEach((file, idx) => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600';
                    div.innerHTML = `
                        <img src="${ev.target.result}" class="w-full h-full object-cover">
                        <button type="button" data-idx="${idx}"
                                class="remove-img absolute top-1 right-1 w-5 h-5 rounded-full bg-black/60 hover:bg-black text-white text-xs flex items-center justify-center">×</button>
                    `;
                    grid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        grid.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-img');
            if (!btn) return;

            const idx = parseInt(btn.dataset.idx, 10);
            const dt = new DataTransfer();
            Array.from(input.files).forEach((f, i) => { if (i !== idx) dt.items.add(f); });
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        });
    })();
</script>
@endpush


<x-recently-viewed :products="$recentlyViewed" />


    </x-sidebar-navbar>
@endsection