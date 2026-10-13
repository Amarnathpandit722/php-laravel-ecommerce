@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Reviews">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <a href="{{ route('reviews.index') }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-gray-400 hover:shadow-md transition">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
            </a>
            <a href="{{ route('reviews.index', ['status' => 'pending']) }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-yellow-500 hover:shadow-md transition">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Pending</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['pending'] }}</p>
            </a>
            <a href="{{ route('reviews.index', ['status' => 'approved']) }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-green-500 hover:shadow-md transition">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Approved</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['approved'] }}</p>
            </a>
            <a href="{{ route('reviews.index', ['status' => 'rejected']) }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-red-500 hover:shadow-md transition">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Rejected</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ $stats['rejected'] }}</p>
            </a>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('reviews.index') }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        All
                    </a>
                    <a href="{{ route('reviews.index', ['status' => 'pending']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request('status') === 'pending' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        Pending
                    </a>
                    <a href="{{ route('reviews.index', ['status' => 'approved']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request('status') === 'approved' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        Approved
                    </a>
                    <a href="{{ route('reviews.index', ['status' => 'rejected']) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request('status') === 'rejected' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                        Rejected
                    </a>
                </div>

                <form action="{{ route('reviews.index') }}" method="GET" class="flex gap-2">
                    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                           class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <button class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 rounded-lg">Search</button>
                </form>
            </div>
        </div>

        {{-- Review cards --}}
        <div class="space-y-4">
            @forelse($reviews as $review)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">

                        {{-- Left: content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name ?? 'User') }}&background=6366f1&color=fff"
                                     class="w-10 h-10 rounded-full" alt="">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $review->user->name ?? 'Deleted user' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        on <a href="{{ route('products.user-show', $review->product) }}"
                                              class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            {{ $review->product->name ?? '—' }}
                                        </a>
                                        · {{ $review->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>

                                <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                    @if($review->status === 'approved') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                    @elseif($review->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                    @else bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 @endif">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </div>

                            @if($review->title)
                                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $review->title }}</h4>
                            @endif
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $review->body }}</p>

                            @if($review->images)
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach($review->image_urls as $url)
                                        <img src="{{ $url }}"
                                             class="w-20 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600 cursor-pointer"
                                             onclick="window.open('{{ $url }}', '_blank')">
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Right: actions --}}
                        <div class="flex flex-row lg:flex-col gap-2 flex-shrink-0">
                            @if($review->status !== 'approved')
                                <form action="{{ route('reviews.approve', $review) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                        ✓ Approve
                                    </button>
                                </form>
                            @endif

                            @if($review->status !== 'rejected')
                                <form action="{{ route('reviews.reject', $review) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium transition">
                                        ✕ Reject
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('reviews.destroy', $review) }}" method="POST"
                                  onsubmit="return confirm('Delete this review permanently?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full px-4 py-2 text-sm border border-red-300 dark:border-red-800 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No reviews match your filters.</p>
                </div>
            @endforelse
        </div>

        @if($reviews->hasPages())
            <div class="mt-6">{{ $reviews->links() }}</div>
        @endif
    </x-sidebar-navbar>
@endsection