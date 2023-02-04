<div>
    @if ($paginator->hasPages())
        @php(isset($this->numberOfPaginatorsRendered[$paginator->getPageName()]) ? $this->numberOfPaginatorsRendered[$paginator->getPageName()]++ : $this->numberOfPaginatorsRendered[$paginator->getPageName()] = 1)
        <div>
            <p class=" text-gray-900 dark:text-white text-center text-sm md:text-base leading-5">
                <span>{!! __('Showing') !!}</span>
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                <span>{!! __('to') !!}</span>
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                <span>{!! __('of') !!}</span>
                <span class="font-medium">{{ $paginator->total() }}</span>
                <span>{!! __('results') !!}</span>
            </p>
        </div>
        <nav role="navigation" aria-label="Pagination Navigation" class="my-2">
            <div class="flex justify-between ">
                <span>
                    @if ($paginator->onFirstPage())
                        <div class=" px-4 py-2 bg-white dark:bg-inherit border border-gray-900 dark:border-gray-500 text-gray-500 cursor-default rounded-md select-none">
                            {!! __('pagination.previous') !!}
                        </div>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class=" px-4 py-2 font-medium text-gray-700 dark:text-white dark:border-white bg-white dark:bg-inherit border-gray-900 border rounded-md active:bg-gray-100 transition ease-in-out duration-150">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="  px-4 py-2 font-medium text-gray-700 dark:text-white dark:border-white bg-white dark:bg-inherit border-gray-900 border rounded-md active:bg-gray-100 transition ease-in-out duration-150">
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <div class=" px-4 py-2 bg-white dark:bg-inherit border border-gray-900 dark:border-gray-500 text-gray-500 cursor-default rounded-md select-none">
                            {!! __('pagination.next') !!}
                        </div>
                    @endif
                </span>
            </div>
        </nav>
    @endif
</div>
