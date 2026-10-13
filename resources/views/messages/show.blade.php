@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Conversation">
        <div class="mb-4">
            <a href="{{ route('messages.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to inbox
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden flex flex-col h-[70vh]">
            {{-- Header --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($conversation->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $conversation->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $conversation->email }}</p>
                </div>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900/50">
                @foreach ($messages as $msg)
                    <div class="flex {{ $msg->from === 'me' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-2xl
                            {{ $msg->from === 'me'
                                ? 'bg-indigo-600 text-white rounded-br-sm'
                                : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-bl-sm' }}">
                            <p class="text-sm">{{ $msg->body }}</p>
                            <p class="text-xs mt-1 {{ $msg->from === 'me' ? 'text-indigo-200' : 'text-gray-400' }}">
                                {{ $msg->created_at->format('H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Composer --}}
            <form action="{{ route('messages.store') }}" method="POST"
                  class="p-4 border-t border-gray-200 dark:border-gray-700 flex gap-2">
                @csrf
                <input type="text" name="message" required placeholder="Type a message..."
                       class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                    Send
                </button>
            </form>
        </div>
    </x-sidebar-navbar>
@endsection