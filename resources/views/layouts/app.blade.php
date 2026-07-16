<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITech - @yield('title', $title ?? 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <aside class="fixed left-0 top-0 flex h-screen w-64 flex-col bg-[#1a5c1a]">
        <div class="border-b border-green-800 px-6 py-5">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/salus-logo.png') }}" alt="Salus Institute of Technology seal" class="h-11 w-11 rounded-full bg-white object-contain p-1">
                <div>
                    <p class="text-sm font-bold text-white">SITech</p>
                    <p class="text-xs text-green-200">Salus Institute of Technology</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <x-dynamic-sidebar />
        </nav>

        <div class="border-t border-green-800 px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#c8a000]">
                    <span class="text-xs font-bold text-green-950">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-green-200">{{ auth()->user()->getRoleNames()->implode(' | ') }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full text-left text-xs text-green-200 transition hover:text-white">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <main class="ml-64 min-h-screen">
        <div class="border-b border-gray-200 bg-white px-8 py-4">
            <h1 class="text-lg font-semibold text-gray-800">@yield('title', $title ?? 'Dashboard')</h1>
        </div>

        <div class="p-8">
            @yield('content')
        </div>
    </main>
</body>
</html>
