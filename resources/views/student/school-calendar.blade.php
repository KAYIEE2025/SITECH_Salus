@extends('layouts.app')
@section('title', 'School Calendar')
@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Calendar Section -->
        <div class="st-card p-6 lg:col-span-3">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">School Calendar</h2>
                <div class="relative">
                    <input type="text" 
                           id="eventSearch" 
                           placeholder="Search events..." 
                           class="border border-gray-300 rounded-lg px-4 py-2 pl-10 text-sm focus:ring-green-500 focus:border-green-500 w-64">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            @if($events->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-500">No school events available.</p>
                </div>
            @else
                <div id="calendar" class="min-h-[500px]"></div>
                
                <!-- Event Color Legend -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Event Legend</h3>
                    <div class="flex flex-wrap gap-4" id="eventLegend">
                        @php
                            $uniqueColors = $events->pluck('color')->filter()->unique();
                        @endphp
                        @if($uniqueColors->isEmpty())
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded" style="background-color: #1a5c1a;"></div>
                                <span class="text-xs text-gray-600">Default Events</span>
                            </div>
                        @else
                            @foreach($uniqueColors as $color)
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded" style="background-color: {{ $color }};"></div>
                                    <span class="text-xs text-gray-600">Event Type</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Upcoming Events Panel -->
        <div class="st-card p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Events</h2>
            
            @if($upcomingEvents->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500 text-sm">No upcoming events.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($upcomingEvents as $event)
        <div class="event-card cursor-pointer rounded-xl border border-green-100 p-4 transition hover:-translate-y-0.5 hover:shadow-md"
                             data-event-id="{{ $event->id }}"
                             data-event-title="{{ $event->title }}"
                             data-event-description="{{ $event->description }}"
                             data-event-date="{{ $event->event_date ? $event->event_date->format('F d, Y') : '' }}"
                             data-event-end-date="{{ $event->event_end_date ? $event->event_end_date->format('F d, Y') : '' }}"
                             data-event-color="{{ $event->color }}">
                            <h3 class="text-sm font-semibold text-gray-800 mb-1">{{ $event->title }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $event->event_date ? $event->event_date->format('M d, Y') : '' }}</p>
                            <p class="text-xs text-gray-600 line-clamp-2">{{ Str::limit($event->description, 80) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Event Modal -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Description</p>
                    <p id="modalDescription" class="text-sm text-gray-800"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Start Date</p>
                        <p id="modalStartDate" class="text-sm text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">End Date</p>
                        <p id="modalEndDate" class="text-sm text-gray-800"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$events->isEmpty())
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                
                const allEvents = [
                    @foreach($events as $event)
                    {
                        title: '{{ $event->title }}',
                        start: '{{ $event->event_date ? $event->event_date->format('Y-m-d') : '' }}',
                        @if($event->event_end_date && $event->event_end_date->gt($event->event_date))
                        end: '{{ $event->event_end_date->copy()->addDay()->format('Y-m-d') }}',
                        @else
                        end: '',
                        @endif
                        backgroundColor: '{{ $event->color ?? '#1a5c1a' }}',
                        borderColor: '{{ $event->color ?? '#1a5c1a' }}',
                        extendedProps: {
                            description: '{{ $event->description }}',
                            eventId: {{ $event->id }}
                        }
                    },
                    @endforeach
                ];

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    buttonText: {
                        today: 'Today',
                        month: 'Month',
                        week: 'Week',
                        day: 'Day',
                        list: 'List'
                    },
                    events: allEvents,
                    eventClick: function(info) {
                        const event = info.event;
                        openModal(
                            event.title,
                            event.extendedProps.description,
                            event.start ? event.start.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '',
                            event.end ? event.end.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : ''
                        );
                    },
                    dayCellDidMount: function(info) {
                        const today = new Date();
                        if (info.date.getDate() === today.getDate() &&
                            info.date.getMonth() === today.getMonth() &&
                            info.date.getFullYear() === today.getFullYear()) {
                            info.el.style.backgroundColor = '#f0fdf4';
                            info.el.style.border = '2px solid #16a34a';
                        }
                    },
                    navLinkDayClick: function(date, jsEvent) {
                        calendar.changeView('timeGridDay', date);
                    },
                    navLinkWeekClick: function(date, jsEvent) {
                        calendar.changeView('timeGridWeek', date);
                    }
                });

                calendar.render();

                // Search functionality
                const searchInput = document.getElementById('eventSearch');
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    
                    if (searchTerm === '') {
                        calendar.removeAllEvents();
                        calendar.addEventSource(allEvents);
                    } else {
                        const filteredEvents = allEvents.filter(event => 
                            event.title.toLowerCase().includes(searchTerm) ||
                            (event.extendedProps.description && event.extendedProps.description.toLowerCase().includes(searchTerm))
                        );
                        calendar.removeAllEvents();
                        calendar.addEventSource(filteredEvents);
                    }
                });

                // Event card click handlers
                document.querySelectorAll('.event-card').forEach(card => {
                    card.addEventListener('click', function() {
                        openModal(
                            this.dataset.eventTitle,
                            this.dataset.eventDescription,
                            this.dataset.eventDate,
                            this.dataset.eventEndDate
                        );
                    });
                });
            });

            function openModal(title, description, startDate, endDate) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('modalDescription').textContent = description || 'No description available.';
                document.getElementById('modalStartDate').textContent = startDate || 'N/A';
                document.getElementById('modalEndDate').textContent = endDate || 'N/A';
                document.getElementById('eventModal').classList.remove('hidden');
                document.getElementById('eventModal').classList.add('flex');
            }

            function closeModal() {
                document.getElementById('eventModal').classList.add('hidden');
                document.getElementById('eventModal').classList.remove('flex');
            }

            // Close modal on outside click
            document.getElementById('eventModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        </script>
    @endif
@endsection
