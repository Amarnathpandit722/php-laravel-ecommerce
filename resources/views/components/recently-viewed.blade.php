{{-- resources/views/components/recently-viewed.blade.php --}}
@props([
    'products' => collect(),
    'title' => 'Your browsing history',
    'showViewAll' => true,
])

@if($products->isNotEmpty())
<section {{ $attributes->merge(['class' => 'mt-10']) }}>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 flex-wrap">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
                @if($showViewAll)
                    <a href="{{ route('products.index') }}"
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline underline-offset-2">
                        View or edit your browsing history
                    </a>
                @endif
            </div>
            <span class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                {{ $products->count() }} {{ Str::plural('item', $products->count()) }}
            </span>
        </div>

        {{-- Carousel --}}
        <div x-data="recentlyViewedCarousel()" class="relative group">

            {{-- Left Arrow --}}
            <button type="button"
                    @click="scroll('left')"
                    :class="canScrollLeft ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center
                           bg-white/95 dark:bg-gray-700/95 shadow-md rounded-full
                           hover:bg-white dark:hover:bg-gray-600 transition
                           border border-gray-200 dark:border-gray-600">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Track --}}
            <div x-ref="track"
                 @scroll.debounce.50ms="updateArrows()"
                 class="flex gap-4 overflow-x-auto scroll-smooth px-6 py-5
                        scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent">

                @foreach($products as $item)
                    <a href="{{ route('products.user-show', $item) }}"
                       class="group/item flex-shrink-0 w-36 sm:w-40 flex flex-col items-center text-center">

                        <div class="w-full aspect-square bg-gray-50 dark:bg-gray-700 rounded-xl overflow-hidden
                                    border border-gray-100 dark:border-gray-600
                                    group-hover/item:border-indigo-400 group-hover/item:shadow-lg
                                    transition-all duration-200 flex items-center justify-center p-2">
                            <img src="{{ $item->image_url }}"
                                 alt="{{ $item->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-contain
                                        group-hover/item:scale-105 transition-transform duration-200">
                        </div>

                        <p class="mt-3 text-xs text-gray-700 dark:text-gray-300 line-clamp-2 leading-snug
                                  group-hover/item:text-indigo-600 dark:group-hover/item:text-indigo-400
                                  transition">
                            {{ $item->name }}
                        </p>

                        @if($item->discount_price)
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($item->discount_price, 2) }}
                            </p>
                        @else
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format($item->price, 2) }}
                            </p>
                        @endif
                    </a>
                @endforeach

            </div>

            {{-- Right Arrow --}}
            <button type="button"
                    @click="scroll('right')"
                    :class="canScrollRight ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center
                           bg-white/95 dark:bg-gray-700/95 shadow-md rounded-full
                           hover:bg-white dark:hover:bg-gray-600 transition
                           border border-gray-200 dark:border-gray-600">
                <svg class="w-5 h-5 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
</section>
@endif

@once
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('recentlyViewedCarousel', () => ({
            canScrollLeft: false,
            canScrollRight: true,

            init() {
                this.$nextTick(() => this.updateArrows());
                window.addEventListener('resize', () => this.updateArrows());
            },

            scroll(direction) {
                const track = this.$refs.track;
                const amount = track.clientWidth * 0.75;
                track.scrollBy({
                    left: direction === 'left' ? -amount : amount,
                    behavior: 'smooth',
                });
            },

            updateArrows() {
                const track = this.$refs.track;
                if (!track) return;
                this.canScrollLeft  = track.scrollLeft > 2;
                this.canScrollRight = track.scrollLeft + track.clientWidth < track.scrollWidth - 2;
            },
        }));
    });
</script>
@endpush
@endonce