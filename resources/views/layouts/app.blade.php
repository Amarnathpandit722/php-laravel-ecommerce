<!DOCTYPE html>
<html    lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">

    <style>[x-cloak] { display: none !important; }

@keyframes toast-in {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}
.toast-item { animation: toast-in 0.25s ease-out; }

    </style>
    <script>window.csrfToken = '{{ csrf_token() }}';</script>

</head>

<body class="bg-gray-100 text-gray-900 transition-colors duration-300 dark:bg-gray-900 dark:text-gray-100">

     {{-- {{ $slot ?? '' }} --}}
     
    @yield('content')

      @stack('scripts')


      {{-- Toast host --}}
<div
    x-data
    class="fixed top-4 right-4 z-[100] space-y-2 max-w-sm"
>
    <template x-for="t in $store.toasts.items" :key="t.id">
        <div
            class="toast-item flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg border"
            :class="{
                'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/40 dark:border-green-800 dark:text-green-200': t.type === 'success',
                'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/40 dark:border-red-800 dark:text-red-200': t.type === 'error',
                'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/40 dark:border-yellow-800 dark:text-yellow-200': t.type === 'warning',
                'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/40 dark:border-blue-800 dark:text-blue-200': t.type === 'info',
            }"
        >
            <span class="text-lg" x-text="t.type === 'success' ? '✅' : t.type === 'error' ? '❌' : t.type === 'warning' ? '⚠️' : 'ℹ️'"></span>
            <p class="flex-1 text-sm" x-text="t.message"></p>
            <button @click="$store.toasts.remove(t.id)" class="opacity-60 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

{{-- Auto-fire toasts from Laravel flash --}}
@if(session('success'))
    <script>document.addEventListener('alpine:init', () => Alpine.store('toasts').success(@json(session('success'))));</script>
@endif
@if(session('error'))
    <script>document.addEventListener('alpine:init', () => Alpine.store('toasts').error(@json(session('error'))));</script>
@endif

</body>
</html>