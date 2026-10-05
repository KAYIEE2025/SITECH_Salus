@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
    @if($latestAnnouncement)
        <div id="announcement-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $latestAnnouncement->title }}</h3>
                        </div>
                        <button onclick="closeAnnouncementModal()" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>{!! nl2br(e($latestAnnouncement->body)) !!}</p>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button onclick="markAnnouncementAsSeen({{ $latestAnnouncement->id }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            Got it
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function closeAnnouncementModal() {
                document.getElementById('announcement-modal').style.display = 'none';
            }

            function markAnnouncementAsSeen(announcementId) {
                const url = '{{ route('teacher.announcements.mark-seen', ['announcement' => ':id']) }}'.replace(':id', announcementId);
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeAnnouncementModal();
                    }
                })
                .catch(error => {
                    console.error('Error marking announcement as seen:', error);
                    closeAnnouncementModal();
                });
            }
        </script>
    @endif
    <div class="mb-6 md:mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] p-4 md:p-6 text-white shadow-lg shadow-green-900/10 sm:p-8">
        <div class="flex flex-col justify-between gap-4 md:gap-6 sm:flex-row sm:items-end">
            <div class="min-w-0">
                <p class="text-xs md:text-sm font-medium text-green-100">Teacher workspace</p>
                <h2 class="mt-1 md:mt-2 text-lg md:text-2xl font-bold tracking-tight sm:text-3xl">Teach, track, and submit with confidence.</h2>
                <p class="mt-1 md:mt-2 max-w-lg text-xs md:text-sm leading-5 md:leading-6 text-green-100">Manage your classes, student lists, announcements, and grade submissions from one focused workspace.</p>
            </div>
            <a href="{{ route('teacher.classes.index') }}" class="rounded-xl bg-gradient-to-r from-[#f4d35e] to-[#d6ad22] px-4 py-3 text-xs md:text-sm font-semibold text-green-950 shadow-md transition hover:shadow-lg min-h-[48px] flex items-center justify-center shrink-0">View my classes</a>
        </div>
    </div>

    <div class="mb-6 md:mb-8 grid grid-cols-1 gap-3 md:gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="tc-card p-4 md:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Assigned Classes</p><span class="rounded-lg bg-green-50 p-1.5 md:p-2 text-green-700 text-xs md:text-sm">◉</span></div><p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900">{{ $totalClasses }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Your current classes</p></div>
        <div class="tc-card p-4 md:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Assigned Students</p><span class="rounded-lg bg-green-50 p-1.5 md:p-2 text-green-700 text-xs md:text-sm">◎</span></div><p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-green-900">{{ $totalStudents }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Students under your classes</p></div>
        <div class="tc-card p-4 md:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Pending Submissions</p><span class="rounded-lg bg-amber-50 p-1.5 md:p-2 text-amber-700 text-xs md:text-sm">!</span></div><p class="mt-3 md:mt-4 text-2xl md:text-3xl font-bold text-amber-700">{{ $pendingSubmissions }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Grades needing attention</p></div>
    </div>

@endsection
