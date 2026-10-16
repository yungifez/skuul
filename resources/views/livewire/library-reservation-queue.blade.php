<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $today = school_today();
        $places = [];
    @endphp

    @if ($canManage)
        <div>
            @if ($isAdding)
                <div class="flex flex-col gap-3 border-y py-4" role="group" aria-label="Put somebody in the queue">
                    <div>
                        <label for="queue-title" class="text-sm text-muted-foreground">Title</label>
                        <select id="queue-title" wire:model.live="titleId" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('titleId') }}>
                            <option value="">Choose the title</option>
                            @foreach ($titles as $title)
                                <option value="{{ $title->id }}">{{ $title->title }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="titleId" class="mt-1" />
                    </div>
                    <div>
                        <label for="queue-borrower" class="text-sm text-muted-foreground">Who is waiting</label>
                        <input id="queue-borrower" type="search" wire:model.live.debounce.300ms="borrowerSearch" autocomplete="off" placeholder="Name or admission number" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('borrowerSearch') }}>
                        <x-field-error name="borrowerSearch" class="mt-1" />
                    </div>
                    @if ($borrowers->isNotEmpty())
                        <ul class="divide-y border-y" aria-label="People who match">
                            @foreach ($borrowers as $borrower)
                                <li wire:key="queue-borrower-{{ $borrower->id }}">
                                    <button type="button" wire:click="reserveFor({{ $borrower->id }})" wire:loading.attr="disabled" wire:target="reserveFor" class="flex min-h-11 w-full select-none items-center justify-between gap-3 py-2 text-left text-sm hover:bg-muted">
                                        <span class="truncate">{{ $borrower->name }}</span>
                                        <span class="shrink-0 text-xs text-muted-foreground">Add to the queue</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @elseif (mb_strlen(trim($borrowerSearch)) >= 2)
                        <p class="text-sm text-muted-foreground">Nobody on this campus matches</p>
                    @endif
                    <div class="flex justify-end">
                        <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopAdding">Done</april:button>
                    </div>
                </div>
            @else
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startAdding">
                    <x-lucide-plus class="mr-2 size-4" aria-hidden="true" />Put somebody in the queue
                </april:button>
            @endif
        </div>
    @endif

    <section class="flex flex-col gap-2" aria-labelledby="ready-heading">
        <h2 id="ready-heading" class="text-base font-semibold">Behind the desk</h2>
        @if ($ready->isEmpty())
            <p class="text-sm text-muted-foreground">Nothing is being kept back</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($ready as $reservation)
                    <li wire:key="ready-{{ $reservation->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $reservation->title?->title ?? '—' }}</p>
                            <p class="text-xs text-muted-foreground">
                                For {{ $reservation->borrower?->name ?? '—' }} · <span class="font-mono">{{ $reservation->copy?->barcode ?? '—' }}</span> ·
                                @if ($reservation->holds_until?->isBefore($today))
                                    <span class="font-medium text-destructive">hold ran out {{ $reservation->holds_until->format('j M') }}</span>
                                @else
                                    kept until {{ $reservation->holds_until?->format('j M') ?? '—' }}
                                @endif
                            </p>
                        </div>
                        @if ($canManage)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $reservation->title?->title }} kept for {{ $reservation->borrower?->name }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="takeOff({{ $reservation->id }})" wire:confirm="Take this reservation off the queue? The copy goes to the next person."><x-lucide-undo-2 class="mr-2 size-4" />Take off the queue</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="flex flex-col gap-2" aria-labelledby="waiting-heading">
        <h2 id="waiting-heading" class="text-base font-semibold">In the queue</h2>
        @if ($waiting->isEmpty())
            <p class="text-sm text-muted-foreground">Nobody is waiting</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($waiting as $reservation)
                    @php($places[$reservation->library_title_id] = ($places[$reservation->library_title_id] ?? 0) + 1)
                    <li wire:key="waiting-{{ $reservation->id }}" class="flex items-center gap-3 py-3">
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted text-xs font-medium tabular-nums" aria-label="Place {{ $places[$reservation->library_title_id] }}">{{ $places[$reservation->library_title_id] }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $reservation->title?->title ?? '—' }}</p>
                            <p class="text-xs text-muted-foreground">{{ $reservation->borrower?->name ?? '—' }} · since {{ $reservation->reserved_on?->format('j M') ?? '—' }}</p>
                        </div>
                        @if ($canManage)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $reservation->borrower?->name }} waiting for {{ $reservation->title?->title }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="takeOff({{ $reservation->id }})" wire:confirm="Take this reservation off the queue?"><x-lucide-undo-2 class="mr-2 size-4" />Take off the queue</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
