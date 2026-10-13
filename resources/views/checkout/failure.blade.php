@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Payment Failed">
        <div class="max-w-2xl mx-auto text-center bg-white dark:bg-gray-800 rounded-2xl shadow p-10">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payment failed</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">{{ $reason }}</p>

            <a href="{{ route('checkout.index') }}"
               class="inline-block mt-6 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                Try again
            </a>
        </div>
    </x-sidebar-navbar>
@endsection