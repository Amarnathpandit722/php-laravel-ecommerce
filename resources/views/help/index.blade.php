@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Help & Support">
        {{-- Hero --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow p-8 text-white mb-6">
            <h2 class="text-2xl font-bold">How can we help you?</h2>
            <p class="text-indigo-100 mt-1">Browse guides or reach out to support.</p>

            <div class="mt-4 relative max-w-lg">
                <input type="text" placeholder="Search help articles..."
                       class="w-full px-4 py-3 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-white focus:outline-none">
                <svg class="absolute right-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @php
                $categories = [
                    ['icon' => '🚀', 'name' => 'Getting Started', 'desc' => 'Setup guides'],
                    ['icon' => '📦', 'name' => 'Products',        'desc' => 'Catalog help'],
                    ['icon' => '🛒', 'name' => 'Orders',          'desc' => 'Sales & shipping'],
                    ['icon' => '👥', 'name' => 'Customers',       'desc' => 'CRM tips'],
                ];
            @endphp

            @foreach($categories as $cat)
                <a href="#" class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 hover:shadow-lg transition">
                    <div class="text-3xl mb-2">{{ $cat['icon'] }}</div>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $cat['name'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $cat['desc'] }}</p>
                </a>
            @endforeach
        </div>

        {{-- FAQ --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Frequently Asked Questions</h3>
            </div>

            @php
                $faqs = [
                    ['q' => 'How do I add a new product?',                'a' => 'Go to Products → Add Product. Fill in the name, category, price, stock, and upload an image.'],
                    ['q' => 'How can I track low-stock items?',           'a' => 'The Inventory page automatically flags products with 5 or fewer units. You can filter to see only low or out-of-stock items.'],
                    ['q' => 'What roles exist and what can they do?',     'a' => 'Admins have full access, Managers handle operations, and Users can only browse and manage their own orders.'],
                    ['q' => 'How do I export reports?',                   'a' => 'From the Reports section you can filter by date range. Export options (PDF/Excel) will be added in a future release.'],
                    ['q' => 'Can I change my password?',                  'a' => 'Yes — go to Settings → Security and follow the prompts.'],
                ];
            @endphp

            <div class="divide-y divide-gray-200 dark:divide-gray-700" x-data="{ open: null }">
                @foreach($faqs as $i => $faq)
                    <div>
                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $faq['q'] }}</span>
                            <svg :class="open === {{ $i }} ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-collapse class="px-4 pb-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Contact --}}
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Still need help?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Email</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">support@myapp.com</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Live Chat</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Mon–Fri, 9am–5pm</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Docs</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Read the full guide</p>
                    </div>
                </div>
            </div>
        </div>
    </x-sidebar-navbar>
@endsection