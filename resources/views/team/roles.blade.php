@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Roles & Permissions">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $roles = [
                    [
                        'name' => 'Admin',
                        'color'=> 'purple',
                        'icon' => '👑',
                        'description' => 'Full access to everything',
                        'permissions' => [
                            'Manage products & categories',
                            'Manage orders & customers',
                            'View analytics & reports',
                            'Manage team members',
                            'Configure system settings',
                        ],
                    ],
                    [
                        'name' => 'Manager',
                        'color'=> 'blue',
                        'icon' => '⚙️',
                        'description' => 'Operational access',
                        'permissions' => [
                            'View products (no create/delete)',
                            'Manage orders & customers',
                            'View inventory & reports',
                            'Cannot manage team',
                        ],
                    ],
                    [
                        'name' => 'User',
                        'color'=> 'green',
                        'icon' => '👤',
                        'description' => 'Basic access',
                        'permissions' => [
                            'Browse products',
                            'View own orders',
                            'Send & receive messages',
                            'Manage own profile',
                        ],
                    ],
                ];
            @endphp

            @foreach($roles as $role)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-{{ $role['color'] }}-100 dark:bg-{{ $role['color'] }}-900/40 flex items-center justify-center text-2xl">
                                {{ $role['icon'] }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $role['name'] }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $role['description'] }}</p>
                            </div>
                        </div>
                    </div>
                    <ul class="p-6 space-y-2">
                        @foreach($role['permissions'] as $perm)
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mt-0.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ $perm }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </x-sidebar-navbar>
@endsection