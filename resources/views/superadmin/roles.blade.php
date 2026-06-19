    @extends('layouts.app')
@section('title', 'Roles & Access')
@section('content')

    <div class="grid grid-cols-2 gap-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="mb-4">
                <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $role['color'] }}">
                    {{ $role['role'] }}
                </span>
            </div>
            <ul class="space-y-2">
                @foreach($role['permissions'] as $permission)
                <li class="flex items-start gap-2 text-sm text-gray-600">
                    <span class="text-green-600 mt-0.5">✓</span>
                    {{ $permission }}
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>

@endsection