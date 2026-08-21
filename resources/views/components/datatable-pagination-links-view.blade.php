<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-muted-foreground">
                Showing <span class="font-medium">{{ $paginator->firstItem() }}</span>
                to <span class="font-medium">{{ $paginator->lastItem() }}</span>
                of <span class="font-medium">{{ $paginator->total() }}</span> results
            </p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <april:button variant="outline" size="sm" type="button" disabled aria-label="{{ __('pagination.previous') }}">
                        <x-lucide-chevron-left class="size-4" />
                    </april:button>
                @else
                    <april:button variant="outline" size="sm" type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" aria-label="{{ __('pagination.previous') }}">
                        <x-lucide-chevron-left class="size-4" />
                    </april:button>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2 text-sm text-muted-foreground" aria-disabled="true">{{ $element }}</span>
                    @elseif (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <april:button variant="default" size="sm" type="button" aria-current="page" disabled>{{ $page }}</april:button>
                            @else
                                <april:button variant="outline" size="sm" type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</april:button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <april:button variant="outline" size="sm" type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" aria-label="{{ __('pagination.next') }}">
                        <x-lucide-chevron-right class="size-4" />
                    </april:button>
                @else
                    <april:button variant="outline" size="sm" type="button" disabled aria-label="{{ __('pagination.next') }}">
                        <x-lucide-chevron-right class="size-4" />
                    </april:button>
                @endif
            </div>
        </nav>
    @endif
</div>
