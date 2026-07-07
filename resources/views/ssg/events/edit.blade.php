@extends('layouts.app')

@section('title', 'Edit SSG Event')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Edit Event</h1>
        <a href="{{ route('ssg.events.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Back to Events</a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <form method="POST" action="{{ route('ssg.events.update', $event) }}">
            @include('ssg.events._form', ['event' => $event])
        </form>
    </div>
@endsection
