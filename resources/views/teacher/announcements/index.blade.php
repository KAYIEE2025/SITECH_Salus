@extends('layouts.app')
@section('title', 'Announcements')
@section('content')
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Announcements</h2>

        @if($announcements->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500">No announcements available.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($announcements as $announcement)
                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-medium text-gray-800">{{ $announcement->title }}</h3>
                            <span class="text-xs text-gray-500">
                                {{ $announcement->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        <div class="text-gray-600 text-sm whitespace-pre-line">
                            {{ $announcement->content }}
                        </div>
                        @if($announcement->poster)
                            <div class="mt-3 text-xs text-gray-500">
                                Posted by: {{ $announcement->poster->name }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
