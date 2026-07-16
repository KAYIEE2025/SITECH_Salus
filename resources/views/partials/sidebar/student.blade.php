<x-nav-link href="{{ route('student.dashboard') }}" :active="request()->routeIs('student.dashboard')">Dashboard</x-nav-link>
<x-nav-link href="{{ route('student.profile.index') }}" :active="request()->routeIs('student.profile*')">My Profile</x-nav-link>
<x-nav-link href="{{ route('student.study-load.index') }}" :active="request()->routeIs('student.study-load*')">Study Load</x-nav-link>
<x-nav-link href="{{ route('student.grades.index') }}" :active="request()->routeIs('student.grades*')">Grades</x-nav-link>
<x-nav-link href="{{ route('student.announcements.index') }}" :active="request()->routeIs('student.announcements*')">Announcements</x-nav-link>
<x-nav-link href="{{ route('student.school-calendar.index') }}" :active="request()->routeIs('student.school-calendar*')">School Calendar</x-nav-link>
<x-nav-link href="{{ route('student.ssg-events.index') }}" :active="request()->routeIs('student.ssg-events*')">SSG Events & Fines</x-nav-link>
