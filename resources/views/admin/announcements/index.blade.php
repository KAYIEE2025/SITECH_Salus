@extends('layouts.app')
@section('title', 'Announcements')
@section('sidebar-links')
    <x-nav-link href="{{ route('admin.dashboard') }}" :active="false">Dashboard</x-nav-link>
    <x-nav-link href="{{ route('admin.announcements.index') }}" :active="true">Announcements</x-nav-link>
    <x-nav-link href="{{ route('admin.calendar.index') }}" :active="false">School Calendar</x-nav-link>
    <x-nav-link href="{{ route('admin.reports.index') }}" :active="false">Reports</x-nav-link>
    <x-nav-link href="{{ route('admin.teacher-study-load') }}" :active="false">Teacher Study Load</x-nav-link>
    <x-nav-link href="{{ route('admin.activity-logs.index') }}" :active="false">Activity Logs</x-nav-link>
    <x-nav-link href="{{ route('admin.profile.index') }}" :active="false">My Profile</x-nav-link>
@endsection
@section('content')
    <x-delete-confirm-modal
        name="delete-announcement-modal"
        title="Delete Announcement"
        message="Are you sure you want to delete this announcement?"
        recordName=""
        recordDetails=""
    />
    @session('success')
        <div class="bg-green-50 border border-green-200 text-green-800 text-xs md:text-sm rounded-lg px-3 py-2 md:px-4 md:py-3 mb-4 md:mb-6">
            {{ $value }}
        </div>
    @endsession
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-xs md:text-sm rounded-lg px-3 py-2 md:px-4 md:py-3 mb-4 md:mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 md:gap-0 mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Announcements</h1>
        <a href="{{ route('admin.announcements.create') }}"
            class="bg-green-800 hover:bg-green-900 text-white text-xs md:text-sm font-medium px-3 py-1.5 md:px-4 md:py-2 rounded-lg transition whitespace-nowrap">
            + New Announcement
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-xs md:text-sm">
                <thead class="bg-gray-50 text-gray-500 text-[10px] md:text-xs uppercase">
                    <tr>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Title</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Target</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Posted By</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Status</th>
                        <th class="text-left px-2 py-2 md:px-6 md:py-3">Date</th>
                        <th class="text-right px-2 py-2 md:px-6 md:py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-2 md:px-6 md:py-4">
                            <p class="font-medium text-gray-800">{{ $announcement->title }}</p>
                            <p class="text-[10px] md:text-xs text-gray-400 truncate max-w-xs md:max-w-md">{{ \Illuminate\Support\Str::limit($announcement->body, 100) }}</p>
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600">
                            @if($announcement->target_type === 'all')
                                <span class="bg-blue-50 text-blue-700 text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full whitespace-nowrap">All Users</span>
                            @elseif($announcement->target_type === 'grade_level')
                                @php $gradeLevel = \App\Models\YearLevel::find($announcement->target_id); @endphp
                                <span class="bg-purple-50 text-purple-700 text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full whitespace-nowrap">
                                    Grade Level: {{ $gradeLevel ? $gradeLevel->name : 'N/A' }}
                                </span>
                            @elseif($announcement->target_type === 'section')
                                @php $section = \App\Models\Section::with('yearLevel')->find($announcement->target_id); @endphp
                                <span class="bg-orange-50 text-orange-700 text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full whitespace-nowrap">
                                    Section: {{ $section ? ($section->yearLevel->name ?? '') . ' - ' . $section->name : 'N/A' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600">{{ $announcement->poster ? $announcement->poster->name : 'System' }}</td>
                        <td class="px-2 py-2 md:px-6 md:py-4">
                            @if($announcement->is_active)
                                <span class="bg-green-50 text-green-700 text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full whitespace-nowrap">Active</span>
                            @else
                                <span class="bg-gray-50 text-gray-700 text-[10px] md:text-xs px-1.5 py-0.5 md:px-2 md:py-1 rounded-full whitespace-nowrap">Inactive</span>
                            @endif
                        </td>
                        <td class="px-2 py-2 md:px-6 md:py-4 text-gray-600">{{ $announcement->created_at ? $announcement->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-2 py-2 md:px-6 md:py-4">
                            <div class="flex justify-end gap-1.5 md:gap-2">
                                <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                    class="text-[10px] md:text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap">
                                    Edit
                                </a>
                                <button
                                    type="button"
                                    x-on:click="$dispatch('open-delete-modal', {
                                        modalName: 'delete-announcement-modal',
                                        id: {{ $announcement->id }},
                                        name: '{{ $announcement->title }}',
                                        details: 'Posted: {{ $announcement->created_at ? $announcement->created_at->format('M d, Y') : 'N/A' }}',
                                        action: '/admin/announcements/{{ $announcement->id }}'
                                    })"
                                    class="text-[10px] md:text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 md:px-3 md:py-1.5 rounded-lg transition whitespace-nowrap"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-2 py-4 md:px-6 md:py-8 text-center text-gray-400 text-[10px] md:text-xs">
                            No announcements yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
        <div class="px-4 py-3 md:px-6 md:py-4 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-2 md:gap-0">
            <p class="text-xs md:text-sm text-gray-600">
                Showing {{ $announcements->firstItem() }} to {{ $announcements->lastItem() }} of {{ $announcements->total() }} entries
            </p>
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
@endsection
