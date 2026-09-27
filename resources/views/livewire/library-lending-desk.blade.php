<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <p class="text-sm text-muted-foreground">
        Lends for {{ $rules->loan_days }} days ·
        {{ $rules->chargesFines() ? $rules->dailyFine()->formatToLocale(app()->getLocale()).' a day late, on the learner\'s account' : 'no fine for late books' }}
    </p>

    @if ($canLend)
        <section class="flex flex-col gap-3" aria-labelledby="scan-heading">
            <div class="flex items-center justify-between gap-3">
                <h2 id="scan-heading" class="text-base font-semibold">Scan a copy</h2>
                <april:dropdown-menu>
                    <slot:trigger>
                        <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More at the desk">
                            <x-lucide-ellipsis class="size-4" />
                        </april:button>
                    </slot:trigger>
                    <slot:content align="end">
                        <april:dropdown-menu-item wire:click="$set('isLendingSet', true)"><x-lucide-library class="mr-2 size-4" />Lend a class set</april:dropdown-menu-item>
                    </slot:content>
                </april:dropdown-menu>
            </div>

            <form wire:submit="scan" class="flex gap-2" aria-label="Scan a copy">
                <div class="flex-1">
                    <label for="desk-barcode" class="sr-only">Barcode</label>
                    <input id="desk-barcode" wire:model="barcode" maxlength="60" autofocus autocomplete="off" placeholder="Barcode" class="{{ $controlClasses }} font-mono" {{ field_error_bindings('barcode') }}>
                </div>
                <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="scan">Find</april:button>
            </form>
            <x-field-error name="barcode" />

            @if ($copy !== null)
                <div class="flex flex-col gap-3 border-y py-3">
                    <div class="flex items-start gap-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $copy->title?->title ?? '—' }}</p>
                            <p class="text-xs text-muted-foreground">
                                <span class="font-mono">{{ $copy->barcode }}</span> ·
                                @if ($copyLoan !== null)
                                    out to {{ $copyLoan->borrower?->name ?? '—' }}, due {{ $copyLoan->due_on->format('j M') }}
                                @else
                                    {{ $copy->status->label() }}
                                @endif
                            </p>
                        </div>
                        <april:button type="button" variant="ghost" class="h-11 shrink-0 select-none" wire:click="clearScan">Clear</april:button>
                    </div>

                    @if ($copyLoan !== null)
                        <div class="flex flex-wrap justify-end gap-2">
                            @if ($copyLoan->daysLate() === 0 && $copyLoan->renewals < $rules->renewals_allowed)
                                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="renew({{ $copyLoan->id }})" wire:loading.attr="disabled" wire:target="renew,takeBack">Renew</april:button>
                            @endif
                            <april:button type="button" class="h-11 select-none" wire:click="takeBack({{ $copyLoan->id }})" wire:loading.attr="disabled" wire:target="renew,takeBack">Take it back</april:button>
                        </div>
                    @elseif ($copy->status->canBeLent())
                        <div>
                            <label for="desk-borrower" class="sr-only">Who is taking it</label>
                            <input id="desk-borrower" type="search" wire:model.live.debounce.300ms="borrowerSearch" autocomplete="off" placeholder="Who is taking it? Name or admission number" class="{{ $controlClasses }}" {{ field_error_bindings('borrowerSearch') }}>
                            <x-field-error name="borrowerSearch" class="mt-1" />
                        </div>
                        @if ($borrowers->isNotEmpty())
                            <ul class="divide-y border-y" aria-label="People who match">
                                @foreach ($borrowers as $borrower)
                                    <li wire:key="borrower-{{ $borrower->id }}">
                                        <button type="button" wire:click="lendTo({{ $borrower->id }})" wire:loading.attr="disabled" wire:target="lendTo" class="flex min-h-11 w-full select-none items-center justify-between gap-3 py-2 text-left text-sm hover:bg-muted">
                                            <span class="truncate">{{ $borrower->name }}</span>
                                            <span class="shrink-0 text-xs text-muted-foreground">Lend</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @elseif (mb_strlen(trim($borrowerSearch)) >= 2)
                            <p class="text-sm text-muted-foreground">Nobody on this campus matches</p>
                        @endif
                    @endif
                </div>
            @endif
        </section>

        @if ($isLendingSet)
            <form wire:submit="lendSet" class="flex flex-col gap-3 border-y py-4" aria-label="Lend a class set">
                <p class="text-sm text-muted-foreground">Gives one copy to every attending learner in the class. The whole set goes out, or nothing does.</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="set-section" class="sr-only">Class</label>
                        <select id="set-section" wire:model="sectionId" class="{{ $controlClasses }}" {{ field_error_bindings('sectionId') }}>
                            <option value="">Choose the class</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->academicYear?->name }} · {{ $section->qualifiedName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="set-title" class="sr-only">Title</label>
                        <select id="set-title" wire:model="titleId" class="{{ $controlClasses }}" {{ field_error_bindings('titleId') }}>
                            <option value="">Choose the title</option>
                            @foreach ($titles as $title)
                                <option value="{{ $title->id }}">{{ $title->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <x-field-error name="sectionId" />
                <x-field-error name="titleId" />
                <div class="flex justify-end gap-2">
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="$set('isLendingSet', false)">Cancel</april:button>
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="lendSet">Lend the set</april:button>
                </div>
            </form>
        @endif
    @endif

    <section class="flex flex-col gap-3" aria-labelledby="out-heading">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 id="out-heading" class="text-base font-semibold">Out now</h2>
            <p class="text-sm text-muted-foreground">
                {{ $openCount }} out
                @if ($overdueCount > 0)
                    · <span class="font-medium text-destructive">{{ $overdueCount }} late</span>
                @endif
            </p>
        </div>

        @if ($openCount === 0)
            <p class="text-sm text-muted-foreground">Everything is on the shelf</p>
        @else
            <div>
                <label for="out-search" class="sr-only">Find a loan</label>
                <input id="out-search" type="search" wire:model.live.debounce.300ms="outSearch" autocomplete="off" placeholder="Find by borrower, title or barcode" class="{{ $controlClasses }}">
            </div>
            @if ($open->isEmpty())
                <p class="text-sm text-muted-foreground">Nothing out matches</p>
            @else
                <ul class="divide-y border-y">
                    @foreach ($open as $loan)
                        @php($late = $loan->daysLate())
                        <li wire:key="loan-{{ $loan->id }}" class="flex items-center gap-3 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $loan->copy?->title?->title ?? '—' }}</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ $loan->borrower?->name ?? '—' }} · <span class="font-mono">{{ $loan->copy?->barcode ?? '—' }}</span> ·
                                    @if ($late > 0)
                                        <span class="font-medium text-destructive">{{ $late }} {{ Str::plural('day', $late) }} late</span>
                                    @else
                                        due {{ $loan->due_on->format('j M') }}
                                    @endif
                                </p>
                            </div>
                            @if ($canLend)
                                <april:button type="button" variant="outline" class="h-11 shrink-0 select-none" wire:click="takeBack({{ $loan->id }})" wire:loading.attr="disabled" wire:target="renew,takeBack">Back</april:button>
                                @if ($late === 0 && $loan->renewals < $rules->renewals_allowed)
                                    <april:dropdown-menu>
                                        <slot:trigger>
                                            <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $loan->copy?->title?->title }} with {{ $loan->borrower?->name }}">
                                                <x-lucide-ellipsis class="size-4" />
                                            </april:button>
                                        </slot:trigger>
                                        <slot:content align="end">
                                            <april:dropdown-menu-item wire:click="renew({{ $loan->id }})"><x-lucide-calendar-plus class="mr-2 size-4" />Renew</april:dropdown-menu-item>
                                        </slot:content>
                                    </april:dropdown-menu>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
    </section>

    @if ($returned->isNotEmpty())
        <section class="flex flex-col gap-3" aria-labelledby="back-heading">
            <h2 id="back-heading" class="text-base font-semibold">Recently back</h2>
            <ul class="divide-y border-y">
                @foreach ($returned as $loan)
                    <li wire:key="back-{{ $loan->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $loan->copy?->title?->title ?? '—' }}</p>
                            <p class="text-xs text-muted-foreground">{{ $loan->borrower?->name ?? '—' }} · back {{ $loan->returned_on?->format('j M') }}</p>
                        </div>
                        @if ($loan->fine_charged > 0)
                            <p class="shrink-0 text-xs text-muted-foreground">Fine {{ $loan->fine()->formatToLocale(app()->getLocale()) }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
