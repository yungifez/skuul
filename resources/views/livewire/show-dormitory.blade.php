<div class="mx-auto flex w-full max-w-4xl flex-col gap-10">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <section aria-label="House summary" class="flex flex-col gap-4">
        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
            <span>{{ $dormitory->label }}</span>
            @if (!$dormitory->is_active)
                <span class="font-medium text-foreground">· Archived</span>
            @endif
            @if ($dormitory->boardingResidence)
                <span>· In {{ $dormitory->boardingResidence->name }}</span>
            @endif
        </p>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4" id="house-occupancy">
            <div>
                <dt class="text-sm text-muted-foreground">Taken</dt>
                <dd class="text-2xl font-semibold tabular-nums">{{ $occupancy['taken'] }} <span class="text-sm font-normal text-muted-foreground">of {{ $occupancy['beds'] }}</span></dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Free</dt>
                <dd class="text-2xl font-semibold tabular-nums">{{ $occupancy['free'] }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Out of use</dt>
                <dd class="text-2xl font-semibold tabular-nums">{{ $occupancy['unavailable'] }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Away tonight</dt>
                <dd class="text-2xl font-semibold tabular-nums">{{ $occupancy['away'] }}</dd>
            </div>
        </dl>

        @if ($onDuty->isNotEmpty())
            <p class="text-sm"><span class="text-muted-foreground">On duty:</span>
                {{ $onDuty->map(fn ($duty) => $duty->user?->name.' ('.$duty->role->label().')')->join(', ') }}</p>
        @endif

        @if ($dormitory->notes)
            <p class="text-sm text-muted-foreground">{{ $dormitory->notes }}</p>
        @endif
    </section>

    @if ($away->isNotEmpty())
        <section aria-labelledby="away-heading" class="flex flex-col gap-3">
            <h2 id="away-heading" class="text-base font-semibold">Out tonight</h2>
            <ul class="divide-y border-y">
                @foreach ($away as $leave)
                    <li wire:key="away-{{ $leave->id }}" class="flex flex-wrap justify-between gap-x-4 py-3 text-sm">
                        <span class="font-medium">{{ $leave->studentRecord?->user?->name }}</span>
                        <span class="text-muted-foreground">{{ $leave->destination }} · back {{ $leave->returns_on?->format('j M') }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section aria-labelledby="rooms-heading" class="flex flex-col gap-3">
        <h2 id="rooms-heading" class="text-base font-semibold">Rooms</h2>

        @if ($dormitory->rooms->isEmpty())
            <p class="text-sm text-muted-foreground">No rooms yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($dormitory->rooms as $room)
                    @php
                        $taken = $room->beds->filter(fn ($bed) => $occupiedBy->has($bed->id))->count();
                        $free = $room->is_active ? $room->beds->filter(fn ($bed) => $bed->is_active && $bed->status === App\Enums\DormitoryBedStatus::Available && !$occupiedBy->has($bed->id))->count() : 0;
                    @endphp
                    <li wire:key="room-{{ $room->id }}" x-data="{ open: false }">
                        <div class="flex items-center gap-2">
                            <button type="button" x-on:click="open = !open" x-bind:aria-expanded="open" aria-controls="room-{{ $room->id }}-beds"
                                class="flex min-h-11 min-w-0 flex-1 items-center gap-3 py-3 text-left select-none">
                                <x-lucide-chevron-right class="size-4 shrink-0 text-muted-foreground transition-transform" x-bind:class="open && 'rotate-90'" aria-hidden="true" />
                                <span class="min-w-0 flex-1">
                                    <span @class(['text-sm font-medium', 'text-muted-foreground' => !$room->is_active])>{{ $room->name }}</span>
                                    @if ($room->floor)
                                        <span class="text-sm text-muted-foreground">· {{ $room->floor }}</span>
                                    @endif
                                    @if (!$room->is_active)
                                        <span class="text-sm text-muted-foreground">· Out of use</span>
                                    @endif
                                </span>
                                <span class="shrink-0 text-sm text-muted-foreground tabular-nums">{{ $taken }}/{{ $room->beds->count() }} taken @if ($free > 0) · {{ $free }} free @endif</span>
                            </button>
                            @if ($canManage)
                                <april:button type="button" variant="ghost" size="icon" class="size-11 shrink-0 select-none" aria-label="Edit {{ $room->name }}" wire:click="editRoom({{ $room->id }})">
                                    <x-lucide-pencil class="size-4" />
                                </april:button>
                            @endif
                        </div>

                        @if ($editingRoomId === $room->id)
                            <form wire:submit="saveRoom" class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-start" aria-label="Edit {{ $room->name }}">
                                <div class="min-w-0 flex-1">
                                    <label for="room-name" class="sr-only">Room name</label>
                                    <input id="room-name" wire:model="roomName" maxlength="60" class="{{ $controlClasses }}" {{ field_error_bindings('roomName') }}>
                                    <x-field-error name="roomName" class="mt-1" />
                                </div>
                                <div class="sm:w-40">
                                    <label for="room-floor" class="sr-only">Floor</label>
                                    <input id="room-floor" wire:model="roomFloor" maxlength="40" placeholder="Floor" class="{{ $controlClasses }}" {{ field_error_bindings('roomFloor') }}>
                                </div>
                                <label for="room-active" class="flex min-h-11 items-center gap-2 text-sm select-none">
                                    <input type="checkbox" id="room-active" wire:model="roomIsActive" {{ field_error_bindings('roomIsActive') }}>
                                    In use
                                </label>
                                <div class="flex gap-2">
                                    <april:button type="button" variant="ghost" class="h-11" wire:click="cancelEditing">Cancel</april:button>
                                    <april:button type="submit" variant="outline" class="h-11">Save</april:button>
                                </div>
                            </form>
                            <x-field-error name="roomIsActive" class="mb-3" />
                        @endif

                        <div id="room-{{ $room->id }}-beds" x-show="open" x-cloak class="mb-3 ml-7 flex flex-col">
                            @forelse ($room->beds as $bed)
                                @php($place = $occupiedBy->get($bed->id))
                                <div wire:key="bed-{{ $bed->id }}" class="border-t first:border-t-0">
                                    <div class="flex items-center justify-between gap-3 py-2">
                                        <div class="min-w-0 text-sm">
                                            <span class="font-medium">{{ $bed->name }}</span>
                                            @if ($place)
                                                <span>· {{ $place->studentRecord?->user?->name }}</span>
                                                <span class="text-muted-foreground">{{ $place->studentRecord?->admission_number }}</span>
                                            @elseif ($bed->status !== App\Enums\DormitoryBedStatus::Available)
                                                <span class="text-muted-foreground">· {{ $bed->status->label() }}@if ($bed->status_reason): {{ $bed->status_reason }}@endif</span>
                                            @else
                                                <span class="text-muted-foreground">· Free</span>
                                            @endif
                                        </div>
                                        @if ($canManage)
                                            <april:dropdown-menu>
                                                <slot:trigger>
                                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="Actions for {{ $room->name }} {{ $bed->name }}">
                                                        <x-lucide-ellipsis class="size-4" />
                                                    </april:button>
                                                </slot:trigger>
                                                <slot:content>
                                                    <april:dropdown-menu-item wire:click="editBed({{ $bed->id }})">
                                                        <x-lucide-pencil class="mr-2 size-4" />Edit bed
                                                    </april:dropdown-menu-item>
                                                    @if ($place)
                                                        <april:dropdown-menu-item wire:click="startLeaving({{ $bed->id }})">
                                                            <x-lucide-log-out class="mr-2 size-4" />End placement
                                                        </april:dropdown-menu-item>
                                                    @endif
                                                </slot:content>
                                            </april:dropdown-menu>
                                        @endif
                                    </div>

                                    @if ($editingBedId === $bed->id)
                                        <form wire:submit="saveBed" class="mb-3 flex flex-col gap-3" aria-label="Edit {{ $bed->name }}">
                                            <div class="grid gap-3 sm:grid-cols-[1fr_12rem]">
                                                <div>
                                                    <label for="bed-name" class="sr-only">Bed name</label>
                                                    <input id="bed-name" wire:model="bedName" maxlength="40" class="{{ $controlClasses }}" {{ field_error_bindings('bedName') }}>
                                                    <x-field-error name="bedName" class="mt-1" />
                                                </div>
                                                <div>
                                                    <label for="bed-status" class="sr-only">Bed status</label>
                                                    <select id="bed-status" wire:model="bedStatus" class="{{ $controlClasses }}" {{ field_error_bindings('bedStatus') }}>
                                                        @foreach (App\Enums\DormitoryBedStatus::cases() as $status)
                                                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <x-field-error name="bedStatus" />
                                            <div class="flex flex-col gap-3 sm:flex-row">
                                                <label for="bed-reason" class="sr-only">Reason</label>
                                                <input id="bed-reason" wire:model="bedStatusReason" maxlength="1000" placeholder="Reason (optional)" class="{{ $controlClasses }} sm:flex-1" {{ field_error_bindings('bedStatusReason') }}>
                                                <div class="flex gap-2">
                                                    <april:button type="button" variant="ghost" class="h-11" wire:click="cancelEditing">Cancel</april:button>
                                                    <april:button type="submit" variant="outline" class="h-11">Save bed</april:button>
                                                </div>
                                            </div>
                                        </form>
                                    @endif

                                    @if ($leavingBedId === $bed->id)
                                        <form wire:submit="endPlacement" class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-start" aria-label="End the placement in {{ $bed->name }}">
                                            <div class="min-w-0 flex-1">
                                                <label for="leave-reason" class="sr-only">Why they are leaving</label>
                                                <input id="leave-reason" wire:model="leaveReason" maxlength="255" placeholder="Why they are leaving" class="{{ $controlClasses }}" {{ field_error_bindings('leaveReason') }}>
                                                <x-field-error name="leaveReason" class="mt-1" />
                                            </div>
                                            <div class="flex gap-2">
                                                <april:button type="button" variant="ghost" class="h-11" wire:click="cancelEditing">Cancel</april:button>
                                                <april:button type="submit" variant="destructive" class="h-11">End placement</april:button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <p class="py-2 text-sm text-muted-foreground">No beds yet.</p>
                            @endforelse

                            @if ($canManage && $room->is_active)
                                <form wire:submit="addBed({{ $room->id }})" class="flex gap-2 border-t pt-3" aria-label="Add a bed to {{ $room->name }}">
                                    <div class="min-w-0 flex-1">
                                        <label for="new-bed-{{ $room->id }}" class="sr-only">Bed name</label>
                                        <input id="new-bed-{{ $room->id }}" wire:model="newBedNames.{{ $room->id }}" maxlength="40" placeholder="Bed {{ $room->beds->count() + 1 }}" class="{{ $controlClasses }}" {{ field_error_bindings('newBedNames.'.$room->id) }}>
                                        <x-field-error name="newBedNames.{{ $room->id }}" class="mt-1" />
                                    </div>
                                    <april:button type="submit" variant="outline" class="h-11 shrink-0">Add bed</april:button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canManage)
            <form wire:submit="addRoom" class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-start" aria-label="Add a room">
                <div class="min-w-0 flex-1">
                    <label for="new-room-name" class="sr-only">Room name</label>
                    <input id="new-room-name" wire:model="newRoomName" maxlength="60" placeholder="New room name" class="{{ $controlClasses }}" {{ field_error_bindings('newRoomName') }}>
                    <x-field-error name="newRoomName" class="mt-1" />
                </div>
                <div class="sm:w-40">
                    <label for="new-room-floor" class="sr-only">Floor</label>
                    <input id="new-room-floor" wire:model="newRoomFloor" maxlength="40" placeholder="Floor" class="{{ $controlClasses }}" {{ field_error_bindings('newRoomFloor') }}>
                </div>
                <april:button type="submit" variant="outline" class="h-11 w-full sm:w-auto">Add room</april:button>
            </form>
        @endif
    </section>

    @if ($canManage && $dormitory->is_active && $assignableBeds->isNotEmpty())
        <section aria-labelledby="place-heading" class="flex flex-col gap-3">
            <h2 id="place-heading" class="text-base font-semibold">Give a learner a bed</h2>
            <form wire:submit="place" class="flex flex-col gap-3">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="place-learner" class="sr-only">Learner</label>
                        <select id="place-learner" wire:model="placeLearnerId" class="{{ $controlClasses }}" {{ field_error_bindings('placeLearnerId') }}>
                            <option value="">Learner</option>
                            @foreach ($learners as $learner)
                                <option value="{{ $learner->id }}">{{ $learner->user?->name }} · {{ $learner->admission_number }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="placeLearnerId" class="mt-1" />
                    </div>
                    <div>
                        <label for="place-bed" class="sr-only">Bed</label>
                        <select id="place-bed" wire:model="placeBedId" class="{{ $controlClasses }}" {{ field_error_bindings('placeBedId') }}>
                            <option value="">Bed</option>
                            @foreach ($assignableBeds as $bed)
                                <option value="{{ $bed->id }}">{{ $bed->room->name }} · {{ $bed->name }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="placeBedId" class="mt-1" />
                    </div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div class="min-w-0 flex-1">
                        <label for="place-reason" class="sr-only">Note</label>
                        <input id="place-reason" wire:model="placeReason" maxlength="255" placeholder="Note (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('placeReason') }}>
                    </div>
                    <april:button type="submit" class="h-11 w-full sm:w-auto" wire:loading.attr="disabled">Give the bed</april:button>
                </div>
            </form>
        </section>
    @endif
</div>
