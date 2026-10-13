@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Messages">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Inbox</h2>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                    {{ $conversations->count() }} conversations
                </span>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($conversations as $conv)
                    <a href="{{ route('messages.show', $conv->id) }}"
                       class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                            {{ strtoupper(substr($conv->name, 0, 1)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-gray-900 dark:text-white truncate">
                                    {{ $conv->name }}
                                </p>
                                @if($conv->unread > 0)
                                    <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-indigo-600 text-white">
                                        {{ $conv->unread }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                {{ $conv->last_message }}
                            </p>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-gray-400">{{ $conv->updated_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <p class="p-12 text-center text-gray-500 dark:text-gray-400">
                        No conversations yet.
                    </p>
                @endforelse
            </div>
        </div>
    </x-sidebar-navbar>
@endsection