<div>
    @if ($paginator->hasPages())
        <div class="flex justify-center items-center border-t border-slate-100 dark:border-slate-700">
            <div class="card-text h-full space-y-10 pt-4">
                @php(isset($this->numberOfPaginatorsRendered[$paginator->getPageName()]) ? $this->numberOfPaginatorsRendered[$paginator->getPageName()]++ : ($this->numberOfPaginatorsRendered[$paginator->getPageName()] = 1))

                <ul class="list-none">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled inline-block" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span
                                class="flex items-center justify-center w-6 h-6 bg-slate-100 dark:bg-slate-700 dark:hover:bg-black-500 text-slate-800
                        dark:text-white rounded mx-[3px] sm:mx-1  hover:text-white text-sm font-Inter font-medium transition-all
                        duration-300 relative top-[2px] pl-2"
                                aria-hidden="true"><iconify-icon icon="material-symbols:arrow-back-ios-rounded"></iconify-icon></span>
                        </li>
                    @else
                        <li class="page-item inline-block">
                            <button type="button"
                                dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                                class="flex items-center justify-center w-6 h-6 bg-slate-100 dark:bg-slate-700 dark:hover:bg-black-500 text-slate-800
                            dark:text-white rounded mx-[3px] sm:mx-1 hover:bg-black-500 hover:text-white text-sm font-Inter font-medium transition-all
                            duration-300 relative top-[2px] pl-2"
                                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                wire:loading.attr="disabled" rel="prev"
                                aria-label="@lang('pagination.previous')"><iconify-icon icon="material-symbols:arrow-back-ios-rounded"></iconify-icon></button>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item inline-block">
                            <button type="button"
                                dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                                class="flex items-center justify-center w-6 h-6 bg-slate-100 dark:bg-slate-700 dark:hover:bg-black-500 text-slate-800
                                dark:text-white rounded mx-[3px] sm:mx-1 hover:bg-black-500 hover:text-white text-sm font-Inter font-medium transition-all
                                duration-300 relative top-[2px]" wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                wire:loading.attr="disabled" rel="next"
                                aria-label="@lang('pagination.next')"><iconify-icon
                                icon="material-symbols:arrow-forward-ios-rounded"></iconify-icon></button>
                        </li>
                    @else
                        <li class="page-item disabled inline-block" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="flex items-center justify-center w-6 h-6 bg-slate-100 dark:bg-slate-700 dark:hover:bg-black-500 text-slate-800
                            dark:text-white rounded mx-[3px] sm:mx-1 hover:bg-black-500 hover:text-white text-sm font-Inter font-medium transition-all
                            duration-300 relative top-[2px]" aria-hidden="true"><iconify-icon
                            icon="material-symbols:arrow-forward-ios-rounded"></iconify-icon></span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    @endif
</div>
