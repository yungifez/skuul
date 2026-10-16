<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $when = fn ($booking) => $booking->starts_at->format('D j M, H:i').' to '.$booking->ends_at->format($booking->ends_at->isSameDay($booking->starts_at) ? 'H:i' : 'D j M, H:i');
    @endphp

    @if (($canBook && $bookable->isNotEmpty()) || $canManage)
        <div class="flex flex-wrap gap-2">
            @if ($canBook && $bookable->isNotEmpty() && !$isBooking)
                <april:button type="button" class="h-11 select-none" wire:click="startBooking">
                    <x-lucide-calendar-plus class="mr-2 size-4" aria-hidden="true" />Book something
                </april:button>
            @endif
            @if ($canManage && !$isSharing && $editingId === null)
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startSharing">
                    <x-lucide-plus class="mr-2 size-4" aria-hidden="true" />Share something new
                </april:button>
            @endif
        </div>
    @endif

    @if ($isBooking)
        <form wire:submit="book" class="flex flex-col gap-3 border-y py-4" aria-label="Book something">
            <div>
                <label for="booking-facility" class="text-sm text-muted-foreground">What</label>
                <select id="booking-facility" wire:model="facilityId" class="{{ $controlClasses }}" {{ field_error_bindings('facilityId') }}>
                    <option value="">Choose what to book</option>
                    @foreach ($bookable as $facility)
                        <option value="{{ $facility->id }}">{{ $facility->name }} · {{ $facility->kind->label() }}</option>
                    @endforeach
                </select>
                <x-field-error name="facilityId" class="mt-1" />
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="booking-from" class="text-sm text-muted-foreground">From</label>
                    <input id="booking-from" type="datetime-local" wire:model="startsAt" required class="{{ $controlClasses }}" {{ field_error_bindings('startsAt') }}>
                </div>
                <div>
                    <label for="booking-to" class="text-sm text-muted-foreground">Until</label>
                    <input id="booking-to" type="datetime-local" wire:model="endsAt" required class="{{ $controlClasses }}" {{ field_error_bindings('endsAt') }}>
                </div>
            </div>
            <x-field-error name="startsAt" />
            <x-field-error name="endsAt" />
            <div>
                <label for="booking-purpose" class="text-sm text-muted-foreground">What it is for</label>
                <input id="booking-purpose" wire:model="purpose" required maxlength="255" placeholder="Speech day rehearsal" class="{{ $controlClasses }}" {{ field_error_bindings('purpose') }}>
                <x-field-error name="purpose" class="mt-1" />
            </div>
            <div class="flex justify-end gap-2">
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopBooking">Cancel</april:button>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="book">Book it</april:button>
            </div>
        </form>
    @endif

    @if ($isSharing || $editingId !== null)
        <form wire:submit="saveFacility" class="flex flex-col gap-3 border-y py-4" aria-label="{{ $editingId === null ? 'Share something new' : 'Change '.$name }}">
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="facility-name" class="text-sm text-muted-foreground">Name</label>
                    <input id="facility-name" wire:model="name" required maxlength="120" placeholder="Main hall" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
                    <x-field-error name="name" class="mt-1" />
                </div>
                <div>
                    <label for="facility-kind" class="text-sm text-muted-foreground">Kind</label>
                    <select id="facility-kind" wire:model="kind" class="{{ $controlClasses }}" {{ field_error_bindings('kind') }}>
                        @foreach ($kinds as $kindOption)
                            <option value="{{ $kindOption->value }}">{{ $kindOption->label() }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="kind" class="mt-1" />
                </div>
                <div>
                    <label for="facility-capacity" class="text-sm text-muted-foreground">How many it holds (optional)</label>
                    <input id="facility-capacity" type="number" inputmode="numeric" min="1" wire:model="capacity" class="{{ $controlClasses }}" {{ field_error_bindings('capacity') }}>
                    <x-field-error name="capacity" class="mt-1" />
                </div>
                <div>
                    <label for="facility-notes" class="text-sm text-muted-foreground">Notes (optional)</label>
                    <input id="facility-notes" wire:model="notes" maxlength="1000" class="{{ $controlClasses }}" {{ field_error_bindings('notes') }}>
                    <x-field-error name="notes" class="mt-1" />
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopSharing">Cancel</april:button>
                <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="saveFacility">{{ $editingId === null ? 'Add it' : 'Save' }}</april:button>
            </div>
        </form>
    @endif

    <section class="flex flex-col gap-2" aria-labelledby="bookings-heading">
        <h2 id="bookings-heading" class="text-base font-semibold">Booked next</h2>
        @if ($bookings->isEmpty())
            <p class="text-sm text-muted-foreground">Nothing is booked</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($bookings as $booking)
                    @php($mayGiveUp = $canManage || ($canBook && $booking->booked_by === $userId))
                    <li wire:key="booking-{{ $booking->id }}" class="flex flex-col gap-2 py-3">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $booking->facility?->name ?? '—' }} · {{ $booking->purpose }}</p>
                                <p class="text-xs text-muted-foreground">{{ $when($booking) }} · {{ $booking->bookedBy?->name ?? '—' }}</p>
                            </div>
                            @if ($mayGiveUp)
                                <april:dropdown-menu>
                                    <slot:trigger>
                                        <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for the booking of {{ $booking->facility?->name }} at {{ $booking->starts_at->format('j M, H:i') }}">
                                            <x-lucide-ellipsis class="size-4" />
                                        </april:button>
                                    </slot:trigger>
                                    <slot:content align="end">
                                        <april:dropdown-menu-item wire:click="startGivingUp({{ $booking->id }})"><x-lucide-undo-2 class="mr-2 size-4" />Give it up</april:dropdown-menu-item>
                                    </slot:content>
                                </april:dropdown-menu>
                            @endif
                        </div>
                        @if ($givingUpId === $booking->id)
                            <form wire:submit="giveUp" class="flex flex-col gap-2 sm:flex-row sm:items-start" aria-label="Give up the booking of {{ $booking->facility?->name }}">
                                <div class="flex-1">
                                    <label for="give-up-reason-{{ $booking->id }}" class="sr-only">Why the booking of {{ $booking->facility?->name }} is given up (optional)</label>
                                    <input id="give-up-reason-{{ $booking->id }}" wire:model="giveUpReason" maxlength="255" placeholder="Why, so the person who booked it knows (optional)" class="{{ $controlClasses }} mt-0" {{ field_error_bindings('giveUpReason') }}>
                                    <x-field-error name="giveUpReason" class="mt-1" />
                                </div>
                                <div class="flex gap-2">
                                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopGivingUp">Keep it</april:button>
                                    <april:button type="submit" variant="destructive" class="h-11 select-none" wire:loading.attr="disabled" wire:target="giveUp">Give it up</april:button>
                                </div>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="flex flex-col gap-2" aria-labelledby="catalogue-heading">
        <h2 id="catalogue-heading" class="text-base font-semibold">What the campus shares</h2>
        @if ($facilities->isEmpty())
            <p class="text-sm text-muted-foreground">Nothing is shared yet. Until something is, a lesson can only happen in the section's own room.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($facilities as $facility)
                    <li wire:key="facility-{{ $facility->id }}" @class(['flex items-center gap-3 py-3', 'text-muted-foreground' => !$facility->is_active])>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $facility->name }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $facility->kind->label() }} · holds {{ $facility->capacity ?? '—' }} ·
                                @if ($facility->is_active)
                                    {{ $facility->upcoming_bookings_count }} booked ahead
                                @else
                                    out of use
                                @endif
                            </p>
                        </div>
                        @if ($canBook && $facility->is_active)
                            <april:button type="button" variant="outline" class="h-11 shrink-0 select-none" wire:click="startBooking({{ $facility->id }})">Book</april:button>
                        @endif
                        @if ($canManage)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $facility->name }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="startChanging({{ $facility->id }})"><x-lucide-pencil class="mr-2 size-4" />Change</april:dropdown-menu-item>
                                    @if ($facility->is_active)
                                        <april:dropdown-menu-item wire:click="retire({{ $facility->id }})" wire:confirm="Take {{ $facility->name }} out of use? Nobody will be able to book it, and {{ $facility->upcoming_bookings_count }} booking(s) ahead will be given up."><x-lucide-archive class="mr-2 size-4" />Take out of use</april:dropdown-menu-item>
                                    @else
                                        <april:dropdown-menu-item wire:click="restore({{ $facility->id }})"><x-lucide-archive-restore class="mr-2 size-4" />Bring back into use</april:dropdown-menu-item>
                                    @endif
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
