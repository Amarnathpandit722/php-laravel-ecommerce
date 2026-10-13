@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Your Cart">
        <div class="max-w-4xl mx-auto space-y-6" x-data="cartPage()">

            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
                    Shopping Cart
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                        ({{ $cart->count }} item{{ $cart->count === 1 ? '' : 's' }})
                    </span>
                </h1>

                @forelse ($cart->items as $item)
                    <div class="flex items-center gap-4 py-4 border-b border-gray-200 dark:border-gray-700 last:border-0"
                         x-data="{ qty: {{ $item->quantity }} }">

                        <img src="{{ $item->product->image_url }}"
                             alt="{{ $item->product->name }}"
                             class="w-20 h-20 rounded-lg object-cover bg-gray-100 dark:bg-gray-700">

                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">
                                {{ $item->product->name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ${{ number_format($item->product->discount_price ?? $item->product->price, 2) }} each
                            </p>
                        </div>

                        {{-- Qty control --}}
                        <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                            <button
                                type="button"
                                @click="updateQty({{ $item->product_id }}, qty - 1, $data)"
                                class="px-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">−</button>
                            <input
                                type="number"
                                min="0"
                                max="{{ $item->product->stock }}"
                                x-model.number="qty"
                                @change="updateQty({{ $item->product_id }}, qty, $data)"
                                class="w-14 text-center bg-transparent border-0 focus:ring-0 text-gray-900 dark:text-white">
                            <button
                                type="button"
                                @click="updateQty({{ $item->product_id }}, qty + 1, $data)"
                                class="px-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">+</button>
                        </div>

                        <p class="w-24 text-right font-semibold text-gray-900 dark:text-white">
                            ${{ number_format(($item->product->discount_price ?? $item->product->price) * $item->quantity, 2) }}
                        </p>

                        <button
                            type="button"
                            @click="removeItem({{ $item->product_id }})"
                            class="p-2 text-gray-400 hover:text-red-500 transition"
                            aria-label="Remove">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @empty
                    <p class="py-8 text-center text-gray-500 dark:text-gray-400">
                        Your cart is empty.
                    </p>
                @endforelse

               @php
    $hasServiceable = \App\Models\Address::where('user_id', auth()->id())
        ->where('is_serviceable', true)->exists();
@endphp

@if ($cart->count > 0)
    <div class="mt-6 flex items-center justify-between">
        <span class="text-gray-600 dark:text-gray-400">Total</span>
        <span class="text-2xl font-bold text-gray-900 dark:text-white">
            ${{ number_format($cart->total, 2) }}
        </span>
    </div>

    @if ($hasServiceable)
        <a href="{{ route('checkout.index') }}"
           class="mt-6 block text-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">
            Proceed to checkout →
        </a>
    @else
        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
            <p class="text-sm text-yellow-800 dark:text-yellow-300 font-medium">
                ⚠️ Add a serviceable address to continue.
            </p>
            <a href="{{ route('addresses.index') }}"
               class="inline-block mt-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-lg">
                Add address
            </a>
        </div>
    @endif
@endif
            </div>
        </div>
    </x-sidebar-navbar>
@endsection

@push('scripts')
<script>
function cartPage() {
    return {
        async updateQty(productId, qty, scope) {
            try {
                const data = await window.api('{{ route('cart.update') }}', {
                    method: 'PATCH',
                    body: { product_id: productId, quantity: qty },
                });
                window.dispatchEvent(new CustomEvent('cart:updated', { detail: data }));
                if (qty <= 0) {
                    location.reload();
                } else {
                    scope.qty = qty;
                }
                window.toast(data.message || 'Cart updated', 'success');
            } catch (e) {
                window.toast(e.message || 'Could not update', 'error');
            }
        },
        async removeItem(productId) {
            try {
                const data = await window.api('{{ route('cart.remove') }}', {
                    method: 'DELETE',
                    body: { product_id: productId },
                });
                window.dispatchEvent(new CustomEvent('cart:updated', { detail: data }));
                location.reload();
            } catch (e) {
                window.toast(e.message || 'Could not remove', 'error');
            }
        },
    }
}
</script>
@endpush