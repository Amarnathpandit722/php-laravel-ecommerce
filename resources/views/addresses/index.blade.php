@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Addresses">
        <div class="max-w-3xl mx-auto space-y-6">

            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Existing addresses --}}
            @foreach ($addresses as $address)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 flex items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $address->label }}</span>
                            @if ($address->is_default)
                                <span class="text-xs px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-full">Default</span>
                            @endif
                            @if ($address->is_serviceable)
                                <span class="text-xs px-2 py-0.5 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-full">Serviceable</span>
                            @else
                                <span class="text-xs px-2 py-0.5 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded-full">Not serviceable</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $address->full_name }} · {{ $address->phone }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $address->formatted }}</p>
                    </div>

                    <form method="POST" action="{{ route('addresses.destroy', $address) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm text-red-600 hover:underline">Remove</button>
                    </form>
                </div>
            @endforeach

            {{-- Add new --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6"
                 x-data="newAddress({ checkUrl: '{{ route('addresses.check') }}' })">

                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Add a new address</h2>

                <form method="POST" action="{{ route('addresses.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Label</label>
                            <input type="text" name="label" value="Home"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Full name</label>
                            <input type="text" name="full_name" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Phone</label>
                            <input type="text" name="phone" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Pincode</label>
                            <input type="number" name="postal_code" required
                            minlength="6"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            title="Please enter a valid 6-digit pincode"
                                   x-model="pincode" @input.debounce.500ms="check()"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p x-show="checking" x-cloak class="text-xs text-gray-500 mt-1">Checking…</p>
                            <p x-show="message && !checking" x-cloak
                               :class="serviceable ? 'text-green-600' : 'text-red-600'"
                               class="text-xs mt-1" x-text="message"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Address line 1</label>
                            <input type="text" name="line1" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Address line 2 (optional)</label>
                            <input type="text" name="line2"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">City</label>
                            <input type="text" name="city" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">State</label>
                            <input type="text" name="state"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Country (ISO-2)</label>
                            <input type="text" name="country" value="IN" maxlength="2" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <button type="submit"
                            :disabled="checking"
                            class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white rounded-lg font-semibold">
                        Save address
                    </button>
                </form>
            </div>
        </div>
    </x-sidebar-navbar>
@endsection

@push('scripts')
<script>
function newAddress({ checkUrl }) {
    return {
        pincode: '',
        checking: false,
        serviceable: false,
        message: '',
        async check() {
            const code = this.pincode?.trim();
            if (!code) { this.message = ''; return; }
            this.checking = true;
            try {
                const data = await window.api(checkUrl, { method: 'POST', body: { postal_code: code } });
                this.serviceable = data.serviceable;
                this.message = data.message;
            } catch (e) {
                this.serviceable = false;
                this.message = 'Could not check serviceability.';
            } finally {
                this.checking = false;
            }
        },
    };
}
</script>
@endpush