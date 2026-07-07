@extends('layouts.app')
@section('title', 'Activity Logs')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="false">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="false">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="true">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Activity Logs</h1>
        <p class="text-sm text-gray-600 mt-1">View all system activity logs (read-only)</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
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
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">
                                {{ $log->description }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $log->causer ? $log->causer->name : 'System' }}
                            @if($log->causer)
                                <span class="text-xs text-gray-400 block">({{ $log->causer->email }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 truncate max-w-md">
                            @php
                                $properties = $log->properties ?? [];
                                $description = isset($properties['attributes']) ? json_encode($properties['attributes']) : 'No details';
                                if (strlen($description) > 100) {
                                    $description = substr($description, 0, 100) . '...';
                                }
                            @endphp
                            {{ $description }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $log->created_at ? $log->created_at->format('M d, Y g:i A') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                            No activity logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
            <p class="text-sm text-gray-600">
                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
            </p>
            {{ $logs->links() }}
        </div>
        @endif
    </div>
@endsection
