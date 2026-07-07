@extends('layouts.app')
@section('title', 'Teacher Dashboard')
@section('content')
    <div class="grid grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Assigned Classes</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalClasses }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Total Assigned Students</p>
            <p class="text-3xl font-bold text-green-800">{{ $totalStudents }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Pending Grade Submissions</p>
            <p class="text-3xl font-bold text-green-800">{{ $pendingSubmissions }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500 mb-1">Latest Announcements</p>
            <p class="text-3xl font-bold text-green-800">{{ $announcements->count() }}</p>
        </div>
    </div>

    @if($announcements->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Latest Announcements</h2>
            <div class="space-y-4">
                @foreach($announcements as $announcement)
                    <div class="border-b border-gray-100 pb-4 last:border-0">
                        <h3 class="font-medium text-gray-800">{{ $announcement->title }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($announcement->content, 150) }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $announcement->created_at->format('M d, Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection