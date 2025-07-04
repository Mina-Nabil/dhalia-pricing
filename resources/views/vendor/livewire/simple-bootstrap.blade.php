<div >
    @if ($paginator->hasPages())
        <nav  class="p-3" style="display: flex; justify-content: center;">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link text-muted">&laquo;</span> {{-- Left arrow --}}
                    </li>
                @else
                    @if(method_exists($paginator,'getCursorName'))
                        <li class="page-item">
                            <button dusk="previousPage" type="button" class="page-link" wire:click="setPage('{{ $paginator->previousCursor()->encode() }}', '{{ $paginator->getCursorName() }}')" wire:loading.attr="disabled" rel="prev">&laquo;</button> {{-- Left arrow --}}
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev">&laquo;</button> {{-- Left arrow --}}
                        </li>
                    @endif
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    @if(method_exists($paginator,'getCursorName'))
                        <li class="page-item">
                            <button dusk="nextPage" type="button" class="page-link" wire:click="setPage('{{ $paginator->nextCursor()->encode() }}', '{{ $paginator->getCursorName() }}')" wire:loading.attr="disabled" rel="next">&raquo;</button> {{-- Right arrow --}}
                        </li>
                    @else
                        <li class="page-item">
                            <button type="button" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="page-link" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next">&raquo;</button> {{-- Right arrow --}}
                        </li>
                    @endif
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link text-muted">&raquo;</span> {{-- Right arrow --}}
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>
