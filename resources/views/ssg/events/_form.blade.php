@csrf

@if($event->exists)
    @method('PUT')
@endif

<div class="mb-4">
    <label class="mb-1 block text-sm font-medium text-gray-700">Event Title <span class="text-red-500">*</span></label>
    <input type="text" name="title" value="{{ old('title', $event->title) }}"
        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="mb-1 block text-sm font-medium text-gray-700">Description</label>
    <textarea name="description" rows="4"
        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">{{ old('description', $event->description) }}</textarea>
    @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="mb-1 block text-sm font-medium text-gray-700">Event Date <span class="text-red-500">*</span></label>
    <input type="date" name="event_date" value="{{ old('event_date', optional($event->event_date)->format('Y-m-d')) }}"
        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
    @error('event_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

<div class="mb-4 grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Event Start Time <span class="text-red-500">*</span></label>
        <input type="time" name="event_start_time" value="{{ old('event_start_time', $event->event_start_time ? \Carbon\Carbon::parse($event->event_start_time)->format('H:i') : '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
        @error('event_start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Event End Time <span class="text-red-500">*</span></label>
        <input type="time" name="event_end_time" value="{{ old('event_end_time', $event->event_end_time ? \Carbon\Carbon::parse($event->event_end_time)->format('H:i') : '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
        @error('event_end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mb-4 grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Scan Start Time</label>
        <input type="time" name="scan_start_time" value="{{ old('scan_start_time', $event->scan_start_time ? \Carbon\Carbon::parse($event->scan_start_time)->format('H:i') : '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
        @error('scan_start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Scan End Time</label>
        <input type="time" name="scan_end_time" value="{{ old('scan_end_time', $event->scan_end_time ? \Carbon\Carbon::parse($event->scan_end_time)->format('H:i') : '') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
        @error('scan_end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mb-4 grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Venue</label>
        <input type="text" name="venue" value="{{ old('venue', $event->venue) }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
        @error('venue') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Fine Amount <span class="text-red-500">*</span></label>
        <input type="number" name="fine_amount" min="0" step="0.01" value="{{ old('fine_amount', $event->fine_amount ?? '0.00') }}"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
        @error('fine_amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="rounded-lg bg-green-800 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-900">
        {{ $event->exists ? 'Save Changes' : 'Create Event' }}
    </button>
    <a href="{{ route('ssg.events.index') }}" class="rounded-lg bg-gray-100 px-6 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-200">
        Cancel
    </a>
</div>
