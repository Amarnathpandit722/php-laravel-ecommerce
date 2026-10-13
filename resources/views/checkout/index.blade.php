@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Checkout">
        <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6"
             x-data="checkoutPage({
                createUrl: '{{ route('checkout.create') }}',
                verifyUrl: '{{ route('checkout.verify') }}',
                razorpayKey: '{{ $razorpayKey }}',
                user: { name: '{{ auth()->user()->name }}', email: '{{ auth()->user()->email }}', contact: '{{ auth()->user()->phone ?? '' }}' }
             })">

            {{-- Left: address + items --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Address section --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Delivery Address</h2>
                        <a href="{{ route('addresses.index') }}"
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            + Add / Manage
                        </a>
                    </div>

                    @if ($serviceable->isEmpty())
                        {{-- Blocking state --}}
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-sm text-red-700 dark:text-red-300 font-medium">
                                ⚠️ No serviceable address found.
                            </p>
                            <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                Add an address where we can deliver, then come back.
                            </p>
                            <a href="{{ route('addresses.index') }}"
                               class="inline-block mt-3 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg">
                                Add a serviceable address
                            </a>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($addresses as $address)
                                @php $ok = $address->is_serviceable; @endphp
                                <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer
                                              {{ $ok ? 'border-gray-200 dark:border-gray-700 hover:border-indigo-400'
                                                     : 'border-red-200 dark:border-red-900 bg-red-50/40 dark:bg-red-900/10 cursor-not-allowed opacity-70' }}">
                                    <input type="radio"
                                           name="address_id"
                                           value="{{ $address->id }}"
                                           x-model.number="selectedAddress"
                                           {{ !$ok ? 'disabled' : '' }}
                                           class="mt-1 text-indigo-600 focus:ring-indigo-500">
                                    <div class="flex-1 text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $address->label }}</span>
                                            @if ($address->is_default)
                                                <span class="text-xs px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-full">Default</span>
                                            @endif
                                            @unless ($ok)
                                                <span class="text-xs px-2 py-0.5 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded-full">Not serviceable</span>
                                            @endunless
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $address->full_name }} · {{ $address->phone }}
                                        </p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $address->formatted }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Cart items --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        Order Summary
                    </h2>

                    @foreach ($cart->items as $item)
                        <div class="flex items-center gap-4 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <img src="{{ $item->product->image_url }}" alt=""
                                 class="w-16 h-16 rounded-lg object-cover bg-gray-100 dark:bg-gray-700">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Qty {{ $item->quantity }} × ₹{{ number_format($item->product->discount_price ?? $item->product->price, 2) }}
                                </p>
                            </div>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                ₹{{ number_format(($item->product->discount_price ?? $item->product->price) * $item->quantity, 2) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: totals + pay button --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 sticky top-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Price Details</h2>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($cart->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span>{{ $cart->total >= 500 ? 'Free' : '₹50.00' }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Tax (18%)</span>
                            <span>₹{{ number_format($cart->total * 0.18, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                        <div class="flex justify-between text-base font-bold text-gray-900 dark:text-white">
                            <span>Total</span>
                            <span>₹{{ number_format($cart->total + ($cart->total >= 500 ? 0 : 50) + ($cart->total * 0.18), 2) }}</span>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="pay()"
                        :disabled="!canPay || processing"
                        class="mt-6 w-full px-6 py-3 rounded-lg font-semibold text-white transition
                               disabled:bg-gray-300 disabled:cursor-not-allowed
                               bg-indigo-600 hover:bg-indigo-700"
                    >
                        <span x-show="!processing">Pay with Razorpay</span>
                        <span x-show="processing" x-cloak>Processing…</span>
                    </button>

                    <p x-show="!selectedAddress" x-cloak class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-center">
                        Select a serviceable address to continue.
                    </p>
                    <p x-show="errorMessage" x-cloak class="mt-2 text-xs text-red-600 text-center" x-text="errorMessage"></p>
                </div>
            </div>
        </div>
    </x-sidebar-navbar>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function checkoutPage({ createUrl, verifyUrl, razorpayKey, user }) {
    return {
        selectedAddress: null,
        processing: false,
        errorMessage: '',

        get canPay() {
            return this.selectedAddress !== null;
        },

        async pay() {
            this.errorMessage = '';
            if (!this.selectedAddress) {
                this.errorMessage = 'Please select an address.';
                return;
            }
            this.processing = true;

            try {
                // 1. Create order on server + Razorpay order
                const res = await window.api(createUrl, {
                    method: 'POST',
                    body: { address_id: this.selectedAddress },
                });

                const { order, razorpay } = res;

                // 2. Open Razorpay checkout modal
                const options = {
                    key: razorpay.key,
                    amount: razorpay.amount,
                    currency: razorpay.currency,
                    name: 'MyApp',
                    description: `Order ${order.number}`,
                    order_id: razorpay.order_id,
                    prefill: {
                        name: user.name,
                        email: user.email,
                        contact: user.contact || '',
                    },
                    theme: { color: '#4f46e5' },
                    handler: async (response) => {
                        // 3. Send to server for verification
                        try {
                            const v = await window.api(verifyUrl, {
                                method: 'POST',
                                body: {
                                    order_id: order.id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_signature: response.razorpay_signature,
                                },
                            });
                            window.location.href = v.redirect;
                        } catch (e) {
                            this.errorMessage = 'Verification failed: ' + (e.message || 'unknown');
                            this.processing = false;
                        }
                    },
                    modal: {
                        ondismiss: () => {
                            this.processing = false;
                        },
                    },
                };

                const rzp = new Razorpay(options);
                rzp.on('payment.failed', (resp) => {
                    this.errorMessage = resp.error?.description || 'Payment failed.';
                    this.processing = false;
                });
                rzp.open();
            } catch (e) {
                this.errorMessage = e.message || 'Could not start payment.';
                this.processing = false;
            }
        },
    };
}
</script>
@endpush