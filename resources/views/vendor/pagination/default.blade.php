@if ($paginator->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small">
        Menampilkan {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} dari {{ $paginator->total() }} item
    </div>
    
    <ul class="pagination pagination-sm mb-0">
        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link ">&lsaquo;</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link " href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link ">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link ">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link " href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link px-2" href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true">
                <span class="page-link px-2">&rsaquo;</span>
            </li>
        @endif
    </ul>
</div>
@endif