<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $learnerName = fn ($leave) => $leave->studentRecord?->user?->name ?? $leave->studentRecord?->admission_number ?? '—';
        $nights = fn ($leave) => $leave->leaves_on->isSameDay($leave->returns_on)
            ? $leave->leaves_on->format('j M')
            : $leave->leaves_on->format('j M').' to '.$leave->returns_on->format('j M');
    @endphp

    @if ($canAsk)
        <div>
            @if ($isAsking)
                <form wire:submit="ask" class="flex flex-col gap-3 border-y py-4" aria-label="Ask for a night away">
                    <div>
                        <label for="leave-learner" class="text-sm text-muted-foreground">Boarder</label>
                        <select id="leave-learner" wire:model="learnerId" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('learnerId') }}>
                            <option value="">Choose a boarder</option>
                            @foreach ($boarders as $boarder)
                                <option value="{{ $boarder->id }}">{{ $boarder->user?->name ?? $boarder->admission_number }} · {{ $boarder->admission_number ?? '—' }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="learnerId" class="mt-1" />
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="leave-from" class="text-sm text-muted-foreground">Leaves on</label>
                            <input id="leave-from" type="date" wire:model="leavesOn" required class="{{ $controlClasses }} mt-1" {{ field_error_bindings('leavesOn') }}>
                        </div>
                        <div>
                            <label for="leave-to" class="text-sm text-muted-foreground">Comes back on</label>
                            <input id="leave-to" type="date" wire:model="returnsOn" required class="{{ $controlClasses }} mt-1" {{ field_error_bindings('returnsOn') }}>
                        </div>
                    </div>
                    <x-field-error name="leavesOn" />
                    <x-field-error name="returnsOn" />
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="leave-destination" class="sr-only">Where they are going</label>
                            <input id="leave-destination" wire:model="destination" required maxlength="150" placeholder="Where they are going" class="{{ $controlClasses }}" {{ field_error_bindings('destination') }}>
                            <x-field-error name="destination" class="mt-1" />
                        </div>
                        <div>
                            <label for="leave-contact" class="sr-only">Who the house can ring (optional)</label>
                            <input id="leave-contact" wire:model="contact" maxlength="100" placeholder="Who the house can ring (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('contact') }}>
                            <x-field-error name="contact" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <label for="leave-reason" class="sr-only">Reason (optional)</label>
                        <input id="leave-reason" wire:model="reason" maxlength="1000" placeholder="Reason (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('reason') }}>
                        <x-field-error name="reason" class="mt-1" />
                    </div>
                    <div class="flex justify-end gap-2">
                        <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopAsking">Cancel</april:button>
                        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="ask">Ask for the night away</april:button>
                    </div>
                </form>
            @else
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startAsking">
                    <x-lucide-plus class="mr-2 size-4" aria-hidden="true" />Ask for a night away
                </april:button>
            @endif
        </div>
    @endif

    @if ($overdue->isNotEmpty())
        <section class="flex flex-col gap-2" aria-labelledby="overdue-heading">
            <h2 id="overdue-heading" class="text-base font-semibold text-destructive">Not back yet</h2>
            <ul class="divide-y border-y">
                @foreach ($overdue as $leave)
                    <li wire:key="overdue-{{ $leave->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $learnerName($leave) }}</p>
                            <p class="text-xs text-muted-foreground">
                                Due back {{ $leave->returns_on->format('j M') }} · {{ $leave->destination }}{{ $leave->contact ? ' · '.$leave->contact : '' }}
                            </p>
                        </div>
                        @if ($canDecide)
                            <april:button type="button" variant="outline" class="h-11 shrink-0 select-none" wire:click="markReturned({{ $leave->id }})" wire:loading.attr="disabled" wire:target="markReturned">Back in</april:button>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="flex flex-col gap-2" aria-labelledby="tonight-heading">
        <h2 id="tonight-heading" class="text-base font-semibold">Out tonight</h2>
        @if ($tonight->isEmpty())
            <p class="text-sm text-muted-foreground">Everybody is in the house</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($tonight as $leave)
                    <li wire:key="tonight-{{ $leave->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $learnerName($leave) }}</p>
                            <p class="text-xs text-muted-foreground">
                                Back {{ $leave->returns_on->format('j M') }} · {{ $leave->destination }}{{ $leave->contact ? ' · '.$leave->contact : '' }}
                            </p>
                        </div>
                        @if ($canDecide)
                            <april:button type="button" variant="outline" class="h-11 shrink-0 select-none" wire:click="markReturned({{ $leave->id }})" wire:loading.attr="disabled" wire:target="markReturned">Back in</april:button>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="flex flex-col gap-2" aria-labelledby="waiting-heading">
        <h2 id="waiting-heading" class="text-base font-semibold">Waiting for a decision</h2>
        @if ($waiting->isEmpty())
            <p class="text-sm text-muted-foreground">Nothing is waiting</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($waiting as $leave)
                    <li wire:key="waiting-{{ $leave->id }}" class="flex flex-col gap-2 py-3">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $learnerName($leave) }}</p>
                                <p class="text-xs text-muted-foreground">{{ $nights($leave) }} · {{ $leave->destination }}{{ $leave->reason ? ' · '.$leave->reason : '' }}</p>
                                @if ($leave->returns_on->isBefore($today))
                                    <p class="text-xs text-destructive">These nights have passed</p>
                                @endif
                            </div>
                            @if ($canDecide && !$leave->returns_on->isBefore($today))
                                <april:button type="button" class="h-11 shrink-0 select-none" wire:click="approve({{ $leave->id }})" wire:loading.attr="disabled" wire:target="approve">Approve</april:button>
                            @endif
                            @if ($canDecide || $canAsk)
                                <april:dropdown-menu>
                                    <slot:trigger>
                                        <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $learnerName($leave) }}">
                                            <x-lucide-ellipsis class="size-4" />
                                        </april:button>
                                    </slot:trigger>
                                    <slot:content align="end">
                                        @if ($canDecide)
                                            <april:dropdown-menu-item wire:click="startRefusing({{ $leave->id }})"><x-lucide-x class="mr-2 size-4" />Refuse</april:dropdown-menu-item>
                                        @endif
                                        <april:dropdown-menu-item wire:click="cancel({{ $leave->id }})" wire:confirm="Call off the night away for {{ $learnerName($leave) }}?"><x-lucide-undo-2 class="mr-2 size-4" />Call off</april:dropdown-menu-item>
                                    </slot:content>
                                </april:dropdown-menu>
                            @endif
                        </div>

                        @if ($refusingId === $leave->id)
                            <form wire:submit="refuse" class="flex flex-col gap-2 sm:flex-row sm:items-start" aria-label="Refuse the night away for {{ $learnerName($leave) }}">
                                <div class="flex-1">
                                    <label for="refuse-note-{{ $leave->id }}" class="sr-only">Why the night away for {{ $learnerName($leave) }} is refused</label>
                                    <input id="refuse-note-{{ $leave->id }}" wire:model="refuseNote" maxlength="1000" required placeholder="Why, so the house can tell the family" class="{{ $controlClasses }}" {{ field_error_bindings('refuseNote') }}>
                                    <x-field-error name="refuseNote" class="mt-1" />
                                </div>
                                <div class="flex gap-2">
                                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopRefusing">Cancel</april:button>
                                    <april:button type="submit" variant="destructive" class="h-11 select-none" wire:loading.attr="disabled" wire:target="refuse">Refuse</april:button>
                                </div>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($upcoming->isNotEmpty())
        <section class="flex flex-col gap-2" aria-labelledby="upcoming-heading">
            <h2 id="upcoming-heading" class="text-base font-semibold">Coming up</h2>
            <ul class="divide-y border-y">
                @foreach ($upcoming as $leave)
                    <li wire:key="upcoming-{{ $leave->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $learnerName($leave) }}</p>
                            <p class="text-xs text-muted-foreground">{{ $nights($leave) }} · {{ $leave->destination }}</p>
                        </div>
                        @if ($canDecide || $canAsk)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $learnerName($leave) }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="cancel({{ $leave->id }})" wire:confirm="Call off the night away for {{ $learnerName($leave) }}?"><x-lucide-undo-2 class="mr-2 size-4" />Call off</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($recent->isNotEmpty())
        <section class="flex flex-col gap-2" aria-labelledby="recent-heading">
            <h2 id="recent-heading" class="text-base font-semibold">Recently closed</h2>
            <ul class="divide-y border-y">
                @foreach ($recent as $leave)
                    <li wire:key="recent-{{ $leave->id }}" class="flex items-start gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $learnerName($leave) }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $nights($leave) }} · {{ $leave->destination }}{{ $leave->decision_note ? ' · '.$leave->decision_note : '' }}
                            </p>
                        </div>
                        <p class="shrink-0 text-xs text-muted-foreground">{{ $leave->status->label() }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
