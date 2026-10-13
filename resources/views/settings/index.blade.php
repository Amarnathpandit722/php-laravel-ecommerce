@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Settings">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('settings.profile') }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your name and email address.</p>
            </a>

            <a href="{{ route('settings.security') }}"
               class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Security</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Change your password.</p>
            </a>
        </div>
    </x-sidebar-navbar>
@endsection