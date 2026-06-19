@props(['href', 'active' => false])

<a href="{{ $href }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm mb-1 transition
    {{ $active
        ? 'bg-yellow-500 text-green-900 font-semibold'
        : 'text-green-300 hover:bg-green-800 hover:text-white' }}">
    {{ $slot }}
</a>