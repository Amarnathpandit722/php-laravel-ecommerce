@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Activity Log">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Recent Activity</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $activities->count() }} events
                </span>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($activities as $activity)
                    <a href="{{ $activity['url'] ?? '#' }}"
                       class="flex items-start gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="w-10 h-10 rounded-full bg-{{ $activity['color'] }}-100 dark:bg-{{ $activity['color'] }}-900/40 flex items-center justify-center text-lg flex-shrink-0">
                            {{ $activity['icon'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $activity['title'] }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $activity['description'] }}
                            </p>
                        </div>
                        <div class="text-xs text-gray-400 flex-shrink-0">
                            {{ $activity['time']->diffForHumans() }}
                        </div>
                    </a>
                @empty
                    <p class="p-12 text-center text-gray-500 dark:text-gray-400">No activity yet.</p>
                @endforelse
            </div>
        </div>
    </x-sidebar-navbar>
@endsection