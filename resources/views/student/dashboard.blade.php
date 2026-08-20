@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    @if($latestAnnouncement)
        <div id="announcement-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $latestAnnouncement->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $latestAnnouncement->created_at->format('M d, Y - g:i A') }}</p>
                        </div>
                        <button onclick="closeAnnouncementModal()" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>{{ nl2br(e($latestAnnouncement->body)) }}</p>
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
                const url = '{{ route('student.announcements.mark-seen', ['announcement' => ':id']) }}'.replace(':id', announcementId);
                
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
    @if(!$student)
        <div class="rounded-2xl border border-yellow-200 bg-gradient-to-br from-yellow-50 to-amber-50 p-6 md:p-8 text-center">
            <h2 class="text-lg md:text-xl font-semibold text-yellow-800">Student Profile Not Found</h2>
            <p class="mt-2 text-xs md:text-sm leading-5 md:leading-6 text-yellow-700">Your student profile has not been encoded yet. Please contact the Registrar's office to complete your profile setup.</p>
        </div>
    @else
        <div class="mb-6 md:mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-[#14532d] via-[#1a5c1a] to-[#0f766e] px-3 py-4 md:px-4 md:py-6 text-white shadow-lg shadow-green-900/10 sm:px-8 sm:py-8">
            <div class="flex flex-col justify-between gap-4 md:gap-6 sm:flex-row sm:items-end">
                <div class="w-full min-w-0">
                    <p class="text-xs md:text-sm font-medium text-green-100">Welcome back, {{ $student->first_name }}</p>
                    <h2 class="mt-1 md:mt-2 max-w-xl text-lg md:text-xl font-bold tracking-tight sm:text-2xl sm:text-3xl">Your student life, in one place.</h2>
                    <p class="mt-1 md:mt-2 max-w-lg text-xs md:text-sm leading-5 md:leading-6 text-green-100">Check your study load, approved grades, school events, and important student information.</p>
                </div>
                <div class="w-full rounded-xl border border-white/20 bg-white/10 px-3 py-2 md:px-4 md:py-3 text-center text-xs md:text-sm backdrop-blur-sm sm:w-auto sm:text-left shrink-0">
                    <p class="text-[10px] md:text-xs text-green-100">School year</p>
                    <p class="mt-1 font-semibold text-xs md:text-sm">{{ $student->school_year }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6 md:mb-8 grid grid-cols-1 gap-3 md:gap-4 px-1 sm:grid-cols-2 sm:px-0 xl:grid-cols-4">
            <div class="st-card w-full p-3 md:p-4 sm:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Subjects Enrolled</p><span class="rounded-lg bg-green-50 p-1.5 md:p-2 text-green-700 text-xs md:text-sm">◉</span></div><p class="mt-3 md:mt-4 text-xl md:text-2xl font-bold text-green-900 sm:text-3xl">{{ $totalSubjects }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Current study load</p></div>
            <div class="st-card w-full p-3 md:p-4 sm:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Approved Grades</p><span class="rounded-lg bg-emerald-50 p-1.5 md:p-2 text-emerald-700 text-xs md:text-sm">✓</span></div><p class="mt-3 md:mt-4 text-xl md:text-2xl font-bold text-green-900 sm:text-3xl">{{ $approvedGrades }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">Officially released</p></div>
            <div class="st-card w-full p-3 md:p-4 sm:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Outstanding Balance</p><span class="rounded-lg bg-amber-50 p-1.5 md:p-2 text-amber-700 text-xs md:text-sm">₱</span></div><p class="mt-3 md:mt-4 break-words text-xl md:text-2xl font-bold {{ $outstandingFineBalance > 0 ? 'text-red-600' : 'text-green-900' }} sm:text-3xl">₱{{ number_format($outstandingFineBalance, 2) }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">SSG fine balance</p></div>
            <div class="st-card w-full p-3 md:p-4 sm:p-5"><div class="flex items-center justify-between"><p class="text-xs md:text-sm text-gray-500">Upcoming Events</p><span class="rounded-lg bg-green-50 p-1.5 md:p-2 text-green-700 text-xs md:text-sm">→</span></div><p class="mt-3 md:mt-4 text-xl md:text-2xl font-bold text-green-900 sm:text-3xl">{{ $upcomingSsgEvents->count() }}</p><p class="mt-1 text-[10px] md:text-xs text-gray-400">SSG activities</p></div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:gap-6 px-1 lg:grid-cols-3 lg:px-0">
            <div class="st-card p-3 md:p-4 sm:p-6 lg:col-span-2">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><h2 class="text-sm md:text-lg font-semibold text-gray-800">Student Information</h2><a href="{{ route('student.profile.index') }}" class="text-xs md:text-xs font-semibold text-green-700 hover:text-green-900">View profile</a></div>
                <div class="mt-4 md:mt-6 grid grid-cols-1 gap-4 md:gap-5 sm:grid-cols-2">
                    <div><p class="text-[10px] md:text-xs font-semibold uppercase tracking-wide text-green-800/70">Student number</p><p class="mt-1 break-all text-sm md:text-base font-medium text-gray-800">{{ $student->student_number }}</p></div>
                    <div><p class="text-[10px] md:text-xs font-semibold uppercase tracking-wide text-green-800/70">Student name</p><p class="mt-1 break-words text-sm md:text-base font-medium text-gray-800">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</p></div>
                    <div><p class="text-[10px] md:text-xs font-semibold uppercase tracking-wide text-green-800/70">Grade level</p><p class="mt-1 text-sm md:text-base font-medium text-gray-800">{{ $student->yearLevel->name ?? 'N/A' }}</p></div>
                    <div><p class="text-[10px] md:text-xs font-semibold uppercase tracking-wide text-green-800/70">Section</p><p class="mt-1 text-sm md:text-base font-medium text-gray-800">{{ $student->section->name ?? 'N/A' }}</p></div>
                </div>
            </div>

            <div class="st-card p-3 md:p-4 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><h2 class="text-sm md:text-lg font-semibold text-gray-800">Student QR Code</h2><span class="rounded-full bg-green-50 px-2 py-0.5 md:px-2.5 md:py-1 text-[10px] font-semibold uppercase tracking-wider text-green-700">ID</span></div>
                <div class="mt-4 md:mt-5 flex min-h-40 md:min-h-48 items-center justify-center rounded-xl border border-green-100 bg-green-50/40 p-2 sm:p-4">
                    @if($student && $student->qr_code_path)
                        <img src="{{ asset('storage/' . $student->qr_code_path) }}" alt="Student QR Code" class="h-28 w-28 md:h-36 md:w-36 max-w-full object-contain sm:h-44 sm:w-44">
                    @else
                        <p class="text-center text-xs md:text-sm text-gray-500">No QR Code Available</p>
                    @endif
                </div>
            </div>
        </div>

        @if($upcomingSsgEvents->isNotEmpty())
            <div class="st-card mt-4 md:mt-6 p-3 md:p-4 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-sm md:text-lg font-semibold text-gray-800">Upcoming SSG Events</h2><p class="mt-1 text-xs md:text-sm text-gray-500">Stay informed about upcoming activities.</p></div><a href="{{ route('student.ssg-events.index') }}" class="text-xs md:text-xs font-semibold text-green-700 hover:text-green-900">View all</a></div>
                <div class="mt-4 md:mt-5 grid gap-2 md:gap-3 grid-cols-1 md:grid-cols-2">
                    @foreach($upcomingSsgEvents as $event)
                        <div class="flex flex-col gap-2 rounded-xl bg-green-50/70 p-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:p-4"><div><p class="font-medium text-gray-800 text-sm md:text-base">{{ $event->title }}</p><p class="mt-1 text-xs md:text-sm text-gray-500">{{ $event->event_date->format('M d, Y - g:i A') }}</p></div><span class="shrink-0 rounded-full bg-green-100 px-2 py-0.5 md:px-2.5 md:py-1 text-[10px] md:text-xs font-medium text-green-700 whitespace-nowrap">{{ $event->event_date->diffForHumans() }}</span></div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
@endsection
