@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="My Reviews">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse($reviews as $review)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-start gap-4">
                        <img src="{{ $review->product->image_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->product->name) }}"
                             class="w-16 h-16 rounded-lg object-cover border border-gray-200 dark:border-gray-600" alt="">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <a href="{{ route('products.user-show', $review->product) }}"
                                   class="font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                    {{ $review->product->name ?? '—' }}
                                </a>
                                <span class="px-2 py-0.5 text-xs rounded-full
                                    @if($review->status === 'approved') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                    @elseif($review->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                    @else bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 @endif">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-0.5 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-gray-400 ml-2">{{ $review->created_at->diffForHumans() }}</span>
                            </div>

                            @if($review->title)
                                <p class="font-medium text-gray-900 dark:text-white">{{ $review->title }}</p>
                            @endif
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $review->body }}</p>

                            @if($review->images)
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach($review->image_urls as $url)
                                        <img src="{{ $url }}" class="w-16 h-16 rounded-lg object-cover cursor-pointer"
                                             onclick="window.open('{{ $url }}', '_blank')">
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex gap-3">
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST"
                                      onsubmit="return confirm('Delete this review?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400">You haven't written any reviews yet.</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-3 text-indigo-600 dark:text-indigo-400 hover:underline">
                        Browse products →
                    </a>
                </div>
            @endforelse
        </div>

        @if($reviews->hasPages())
            <div class="mt-6">{{ $reviews->links() }}</div>
        @endif
    </x-sidebar-navbar>
@endsection