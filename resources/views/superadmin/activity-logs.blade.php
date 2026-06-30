@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="rounded-xl border border-gray-200 bg-white">
        <div class="border-b border-gray-100 px-6 py-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">System Activity Logs</h2>
                    <p class="mt-1 text-xs text-gray-500">Review login, account, grade, QR, and other system activity.</p>
                </div>
                <a href="{{ route('superadmin.activity-logs') }}" class="text-xs font-semibold text-[#1a5c1a] hover:text-green-900">
                    Reset filters
                </a>
            </div>

            <form method="GET" action="{{ route('superadmin.activity-logs') }}" class="mt-4 grid grid-cols-4 gap-3">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search description"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">

                <select name="causer_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    <option value="">All users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((string) request('causer_id') === (string) $user->id)>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                <select name="event" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a5c1a]">
                    <option value="">All events</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" @selected(request('event') === $event)>
                            {{ ucfirst($event) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                    Apply Filters
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Event</th>
                        <th class="px-6 py-3 text-left">Performed By</th>
                        <th class="px-6 py-3 text-left">Description</th>
                        <th class="px-6 py-3 text-left">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">
                                    {{ ucfirst($log->event ?? 'activity') }}
                                </span>
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-800">
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
                                No activity logs match the current filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
