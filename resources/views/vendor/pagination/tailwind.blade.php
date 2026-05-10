@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 text-sm font-bold text-gray-400 bg-gray-200 rounded-full cursor-not-allowed">
                &laquo; Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 text-sm font-bold text-white bg-[#094174] rounded-full hover:bg-[#105DA3] transition shadow-sm">
                &laquo; Prev
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div class="hidden md:flex gap-2">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-4 py-2 text-sm font-bold text-gray-400">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-4 py-2 text-sm font-bold text-white bg-[#094174] rounded-full shadow-md">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-sm font-bold text-[#1a2b4c] bg-white border border-gray-200 rounded-full hover:bg-gray-50 transition shadow-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 text-sm font-bold text-white bg-[#094174] rounded-full hover:bg-[#105DA3] transition shadow-sm">
                Next &raquo;
            </a>
        @else
            <span class="px-4 py-2 text-sm font-bold text-gray-400 bg-gray-200 rounded-full cursor-not-allowed">
                Next &raquo;
            </span>
        @endif
    </nav>
@endif