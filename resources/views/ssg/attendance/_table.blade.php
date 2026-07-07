<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
                <th class="px-6 py-3 text-left">Student Number</th>
                <th class="px-6 py-3 text-left">Student Name</th>
                <th class="px-6 py-3 text-left">Grade & Section</th>
                <th class="px-6 py-3 text-left">Attendance Status</th>
                <th class="px-6 py-3 text-left">Scan Time</th>
                <th class="px-6 py-3 text-left">Fine Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($attendances as $attendance)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $attendance->student->student_number ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $attendance->student->full_name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $attendance->student?->yearLevel?->name ?? 'N/A' }}
                        @if($attendance->student?->section)
                            - Section {{ $attendance->student->section->name }}
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($attendance->is_present)
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Present</span>
                        @else
                            <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700">Absent</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $attendance->scanned_at ? $attendance->scanned_at->format('M d, Y h:i A') : 'Not scanned' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($attendance->is_present)
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700">Waived</span>
                        @else
                            <span class="rounded-full bg-yellow-50 px-2.5 py-1 text-xs font-medium text-yellow-700">{{ $attendance->payment_status ?? 'Unpaid' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No attendance records prepared for this event.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
