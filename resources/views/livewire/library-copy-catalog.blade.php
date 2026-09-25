<div class="space-y-4" wire:loading.class="opacity-60" wire:target="search,clearSearch,gotoPage,previousPage,nextPage">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="min-w-0 flex-1 space-y-1.5">
            <label for="library-search" class="text-sm font-medium">Search copies</label>
            <input id="library-search" type="search" wire:model.live.debounce.300ms="search"
                placeholder="Title, author, ISBN, or barcode"
                class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
        </div>
        @if ($search !== '')
            <april:button type="button" variant="outline" wire:click="clearSearch" wire:loading.attr="disabled">Clear search</april:button>
        @endif
    </div>

    <p wire:loading class="text-sm text-muted-foreground" role="status" aria-live="polite">Updating copies…</p>

    <section aria-labelledby="library-copies-heading" class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-4 sm:p-6">
            <h3 id="library-copies-heading" class="text-lg font-semibold leading-none tracking-tight">Copies</h3>
            <p class="text-sm text-muted-foreground">Where each copy is, and who has it.</p>
        </div>

        @if ($copies->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-library class="size-6" />
                </span>
                <p class="text-sm font-medium">{{ $search === '' ? 'Nothing on the shelf yet.' : 'Nothing matches that search.' }}</p>
                @if ($search !== '')
                    <april:button type="button" variant="outline" wire:click="clearSearch">Show all copies</april:button>
                @endif
            </div>
        @else
            <div class="grid gap-3 p-4 md:hidden">
                @foreach ($copies as $copy)
                    @php ($loan = $copy->loans->first())
                    <article wire:key="mobile-library-copy-{{ $copy->id }}" class="rounded-lg border bg-background p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h4 class="break-words font-medium">{{ $copy->title?->title }}</h4>
                                <p class="mt-1 break-words font-mono text-xs text-muted-foreground">{{ $copy->barcode }}</p>
                            </div>
                            @if ($loan !== null)
                                <span class="shrink-0 text-xs text-muted-foreground">On loan</span>
                            @endif
                        </div>
                        <dl class="mt-4 grid gap-3 border-t pt-4 text-sm">
                            <div>
                                <dt class="text-muted-foreground">Author</dt>
                                <dd class="mt-1 break-words font-medium">{{ $copy->title?->authors }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Where it is</dt>
                                <dd class="mt-1 break-words font-medium">{{ $loan !== null ? 'Out on loan' : $copy->status->label() }}</dd>
                            </div>
                            <div>
                                <dt class="text-muted-foreground">Who has it</dt>
                                <dd class="mt-1 break-words font-medium">{{ $loan !== null ? $loan->borrower?->name.' · due '.$loan->due_on?->format('j M') : '—' }}</dd>
                            </div>
                        </dl>
                        @if ($canManage && $loan === null && $copy->status->isHeld())
                            <form action="{{ route('library-copies.destroy', $copy->id) }}" method="POST" class="mt-4 border-t pt-4"
                                data-confirm="Withdraw this copy from the shelves?">
                                @csrf
                                @method('DELETE')
                                <april:button type="submit" variant="ghost" size="sm" class="w-full">Withdraw</april:button>
                            </form>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 font-medium">Barcode</th>
                            <th class="p-4 font-medium">Title</th>
                            <th class="p-4 font-medium">Author</th>
                            <th class="p-4 font-medium">Where it is</th>
                            <th class="p-4 font-medium">Who has it</th>
                            <th class="p-4 font-medium"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($copies as $copy)
                            @php ($loan = $copy->loans->first())
                            <tr wire:key="desktop-library-copy-{{ $copy->id }}" class="border-b last:border-0">
                                <td class="p-4 font-mono text-xs">{{ $copy->barcode }}</td>
                                <td class="p-4 font-medium">{{ $copy->title?->title }}</td>
                                <td class="p-4 text-muted-foreground">{{ $copy->title?->authors }}</td>
                                <td class="p-4">{{ $loan !== null ? 'Out on loan' : $copy->status->label() }}</td>
                                <td class="p-4 text-muted-foreground">
                                    @if ($loan !== null)
                                        {{ $loan->borrower?->name }} &middot; due {{ $loan->due_on?->format('j M') }}
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    @if ($canManage && $loan === null && $copy->status->isHeld())
                                        <form action="{{ route('library-copies.destroy', $copy->id) }}" method="POST"
                                            data-confirm="Withdraw this copy from the shelves?">
                                            @csrf
                                            @method('DELETE')
                                            <april:button type="submit" variant="ghost" size="sm">Withdraw</april:button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t p-4">{{ $copies->links() }}</div>
        @endif
    </section>
</div>
