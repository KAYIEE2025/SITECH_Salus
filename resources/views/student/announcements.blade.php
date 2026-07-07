@extends('layouts.app')
@section('title', 'Announcements')
@section('content')
    @if(!$student)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <h2 class="text-xl font-semibold text-yellow-800 mb-2">Student Profile Not Found</h2>
            <p class="text-yellow-700">Your student profile has not yet been created. Please contact the Registrar.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Announcements</h2>
            </div>

            <!-- Search Box -->
            <form action="{{ route('student.announcements.index') }}" method="GET" class="mb-6">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search announcements by title or content..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 text-sm focus:ring-green-500 focus:border-green-500">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </form>

            @if($announcements->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500">No announcements available.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4">
                    @foreach($announcements as $announcement)
                        <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-base font-semibold text-gray-800">{{ $announcement->title }}</h3>
                                <span class="text-xs text-gray-500 whitespace-nowrap ml-4">
                                    {{ $announcement->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 mb-4 whitespace-pre-line">{{ $announcement->body }}</div>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Posted by: {{ $announcement->poster->name ?? 'System' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
@endsection
