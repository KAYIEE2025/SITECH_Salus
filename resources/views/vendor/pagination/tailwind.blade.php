@if ($paginator->hasPages())
    <nav class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed" aria-disabled="true">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg transition" rel="prev">
                    Previous
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2 text-sm text-gray-500">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-2 text-sm font-medium text-white bg-green-800 rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-2 text-sm text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg transition">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg transition" rel="next">
                    Next
                </a>
            @else
                <span class="px-3 py-2 text-sm text-gray-400 cursor-not-allowed" aria-disabled="true">
                    Next
                </span>
            @endif
        </div>

        {{-- Info Text --}}
        <div class="text-sm text-gray-500">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
    </nav>
@endif
