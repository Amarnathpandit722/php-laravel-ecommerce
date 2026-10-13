@extends('layouts.app')

@section('content')
    <x-sidebar-navbar title="Team">
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-4 py-3 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-gray-400">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-purple-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Admins</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $stats['admins'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Managers</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['managers'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5 border-l-4 border-green-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-medium">Users</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['users'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Team Members</h3>

                <form action="{{ route('team.index') }}" method="GET" class="flex gap-2">
                    <select name="role" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All roles</option>
                        <option value="admin"   @selected(request('role') === 'admin')>Admin</option>
                        <option value="manager" @selected(request('role') === 'manager')>Manager</option>
                        <option value="user"    @selected(request('role') === 'user')>User</option>
                    </select>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                           class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <button class="px-3 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Filter</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff"
                                             class="w-9 h-9 rounded-full" alt="">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                            <span class="text-xs text-gray-400">(you)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($user->role === 'admin') bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300
                                        @elseif($user->role === 'manager') bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">{{ $users->links() }}</div>
            @endif
        </div>
    </x-sidebar-navbar>
@endsection