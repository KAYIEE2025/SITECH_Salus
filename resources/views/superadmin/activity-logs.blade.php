@extends('layouts.app')

@section('title', 'Activity Logs')

<!-- @section('sidebar-links')
    <x-nav-link href="{{ route('superadmin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('superadmin.accounts') }}" :active="false">Manage Accounts</x-nav-link>
    <x-nav-link href="{{ route('superadmin.activity-logs') }}" :active="true">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('superadmin.profile') }}" :active="false">My Profile</x-nav-link>
@endsection -->

@section('content')

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">System Activity Logs</h2>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs">
                <tr>
                    <th class="text-left px-6 py-3">Action</th>
                    <th class="text-left px-6 py-3">Performed By</th>
                    <th class="text-left px-6 py-3">Description</th>
                    <th class="text-left px-6 py-3">Date & Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">
                            {{ $log->event ?? 'action' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-gray-800 font-medium">
                        {{ optional($log->causer)->name ?? 'System' }}
                    </td>
                    <td class="px-6 py-3 text-gray-500">
                        {{ $log->description }}
                    </td>
                    <td class="px-6 py-3 text-gray-400">
                        {{ $log->created_at->format('M d, Y h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                        No activity logs yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif

    </div>

@endsection