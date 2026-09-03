@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('dormitories.show', $dormitory->id), 'text' => $dormitory->name, 'active'],
]])

@section('title', $dormitory->name)

@section('page_heading', $dormitory->name)

@section('page_actions')
    @if ($canManage)
        <april:button-link href="{{ route('dormitories.edit', $dormitory->id) }}" variant="outline">Edit house</april:button-link>
        @if ($dormitory->is_active)
            <form action="{{ route('dormitories.destroy', $dormitory->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <april:button type="submit" variant="ghost">Archive house</april:button>
            </form>
        @endif
    @endif
@endsection

@section('content')
@php
    $assignableBeds = $dormitory->rooms->flatMap(
        fn ($room) => $room->beds->filter(
            fn ($bed) => $room->is_active
                && $bed->is_active
                && $occupiedBy->get($bed->id) === null
                && $bed->status === \App\Enums\DormitoryBedStatus::Available,
        ),
    );
@endphp
<div x-data="boardingRooms(@js($roomDetails))" class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <div>
        <div class="flex flex-wrap items-center gap-2">
            <p class="text-xs font-medium uppercase text-muted-foreground">{{ $dormitory->label }}</p>
            @if (!$dormitory->is_active)
                <april:badge variant="outline">Archived</april:badge>
            @endif
        </div>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-foreground md:text-3xl">{{ $dormitory->name }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            {{ $occupancy['taken'] }} occupied, {{ $occupancy['free'] }} available, and {{ $occupancy['unavailable'] }} unavailable out of {{ $occupancy['beds'] }} active beds. {{ $occupancy['away'] }} learners are out tonight.
        </p>
        @if ($dormitory->notes)
            <p class="mt-3 max-w-2xl text-sm text-muted-foreground">{{ $dormitory->notes }}</p>
        @endif
    </div>

    <x-display-validation-errors />

    @if ($onDuty->isNotEmpty())
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase text-muted-foreground">On duty</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($onDuty as $duty)
                    <april:badge variant="outline">{{ $duty->user?->name }} &middot; {{ $duty->role->label() }}</april:badge>
                @endforeach
            </div>
        </div>
    @endif

    @if ($away->isNotEmpty())
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase text-muted-foreground">Out of the house tonight</p>
            <ul class="mt-3 flex flex-col gap-2">
                @foreach ($away as $leave)
                    <li class="text-sm">
                        <span class="font-medium">{{ $leave->studentRecord?->user?->name }}</span>
                        <span class="text-muted-foreground">&middot; {{ $leave->destination }} &middot; back {{ $leave->returns_on?->format('j M') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Rooms</h3>
            <p class="text-sm text-muted-foreground">Each room can have a different number of beds. Rooms with current boarders cannot be taken out of use.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="border-b bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">Room</th>
                        <th scope="col" class="px-6 py-3 font-medium">Floor</th>
                        <th scope="col" class="px-6 py-3 font-medium">Capacity</th>
                        <th scope="col" class="px-6 py-3 font-medium">Status</th>
                        <th scope="col" class="px-6 py-3 font-medium">View</th>
                        @if ($canManage)
                            <th scope="col" class="px-6 py-3 font-medium">Edit</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($dormitory->rooms as $room)
                        @php($roomSummary = $roomSummaries[$room->id])
                        <tr class="align-middle {{ $room->is_active ? '' : 'text-muted-foreground' }}">
                            <td class="align-middle px-6 py-4 font-medium text-foreground">{{ $room->name }}</td>
                            <td class="align-middle px-6 py-4">{{ $room->floor ?: '—' }}</td>
                            <td class="align-middle px-6 py-4">
                                <p class="font-medium text-foreground">{{ $roomSummary['bed_count'] }} {{ Str::plural('bed', $roomSummary['bed_count']) }}</p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ $roomSummary['available_count'] }} available · {{ $roomSummary['occupied_count'] }} occupied
                                    @if ($roomSummary['unavailable_count'] > 0)
                                        · {{ $roomSummary['unavailable_count'] }} unavailable
                                    @endif
                                </p>
                            </td>
                            <td class="align-middle px-6 py-4">
                                @if ($room->is_active)
                                    <april:badge variant="secondary">In use</april:badge>
                                @else
                                    <april:badge variant="outline">Out of use</april:badge>
                                @endif
                            </td>
                            <td class="align-middle px-6 py-4">
                                <button type="button" @click="openRoom({{ $room->id }})"
                                    class="inline-flex h-9 items-center justify-center gap-2 rounded-md border border-input bg-background px-3 text-sm font-medium shadow-sm transition-colors hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                    <x-lucide-eye class="size-4" />
                                    View beds
                                </button>
                            </td>
                            @if ($canManage)
                                <td class="align-middle px-6 py-4">
                                    <form action="{{ route('dormitory-rooms.update', $room->id) }}" method="POST" class="flex min-w-[360px] flex-wrap items-end gap-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex flex-col gap-1">
                                            <label for="room-name-{{ $room->id }}" class="text-xs font-medium">Name</label>
                                            <input id="room-name-{{ $room->id }}" name="name" required maxlength="60" value="{{ $room->name }}"
                                                class="flex h-8 w-32 rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <label for="room-floor-{{ $room->id }}" class="text-xs font-medium">Floor</label>
                                            <input id="room-floor-{{ $room->id }}" name="floor" maxlength="40" value="{{ $room->floor }}"
                                                class="flex h-8 w-28 rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                        </div>
                                        <label class="flex h-8 items-center gap-1 text-xs">
                                            <input type="hidden" name="is_active" value="0">
                                            <input name="is_active" type="checkbox" value="1" @checked($room->is_active)>
                                            In use
                                        </label>
                                        <april:button type="submit" variant="outline" size="sm">Save</april:button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 6 : 5 }}" class="align-middle px-6 py-8 text-center text-sm text-muted-foreground">This house has no rooms yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($canManage)
            <form action="{{ route('dormitory-rooms.store', $dormitory->id) }}" method="POST" class="flex flex-wrap items-end gap-2 border-t p-6">
                @csrf
                <div class="flex flex-col gap-1">
                    <label for="new-room-name" class="text-xs font-medium">Add a room</label>
                    <input id="new-room-name" name="name" required maxlength="60" placeholder="Room name"
                        class="flex h-9 w-44 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
                <div class="flex flex-col gap-1">
                    <label for="new-room-floor" class="text-xs font-medium">Floor <span class="font-normal text-muted-foreground">(optional)</span></label>
                    <input id="new-room-floor" name="floor" maxlength="40" placeholder="Ground floor"
                        class="flex h-9 w-36 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
                <april:button type="submit" size="sm">Add room</april:button>
            </form>
        @endif
    </div>

    <april:dialog dismissable x-model="roomModalOpen" x-teleport="body">
        <slot:content class="max-w-4xl">
            <april:dialog-header>
                <slot:title>
                    <span x-text="selectedRoom ? selectedRoom.name : 'Room details'"></span>
                </slot:title>
                <slot:description>
                    <span x-text="selectedRoom ? (selectedRoom.floor || 'No floor specified') : ''"></span>
                </slot:description>
            </april:dialog-header>

            <template x-if="selectedRoom">
                <div class="space-y-5">
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-lg border bg-muted/20 p-3">
                            <p class="text-xs font-medium uppercase text-muted-foreground">Capacity</p>
                            <p class="mt-1 text-lg font-semibold" x-text="selectedRoom.bed_count"></p>
                        </div>
                        <div class="rounded-lg border bg-muted/20 p-3">
                            <p class="text-xs font-medium uppercase text-muted-foreground">Available</p>
                            <p class="mt-1 text-lg font-semibold" x-text="selectedRoom.available_count"></p>
                        </div>
                        <div class="rounded-lg border bg-muted/20 p-3">
                            <p class="text-xs font-medium uppercase text-muted-foreground">Occupied</p>
                            <p class="mt-1 text-lg font-semibold" x-text="selectedRoom.occupied_count"></p>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg border">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b bg-muted/20 px-4 py-3">
                            <div>
                                <h4 class="font-medium">Beds in this room</h4>
                                <p class="mt-1 text-xs text-muted-foreground">View occupants, availability, and bed-specific actions here.</p>
                            </div>
                            <span class="text-sm text-muted-foreground" x-text="selectedRoom.bed_count + ' total'"></span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[640px] text-left text-sm">
                                <thead class="border-b bg-muted/40 text-xs uppercase text-muted-foreground">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 font-medium">Bed</th>
                                        <th scope="col" class="px-4 py-3 font-medium">Status</th>
                                        <th scope="col" class="px-4 py-3 font-medium">Occupant</th>
                                        @if ($canManage)
                                            <th scope="col" class="px-4 py-3 font-medium">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <template x-for="bed in selectedRoom.beds" :key="bed.id">
                                        <tr class="align-middle">
                                            <td class="align-middle px-4 py-3 font-medium" x-text="bed.name"></td>
                                            <td class="align-middle px-4 py-3">
                                                <span x-show="bed.is_occupied" class="inline-flex rounded-full border px-2 py-1 text-xs font-medium">Occupied</span>
                                                <span x-show="!bed.is_occupied" class="inline-flex rounded-full border px-2 py-1 text-xs font-medium" x-text="bed.status_label"></span>
                                                <p x-show="bed.status_reason" x-text="bed.status_reason" class="mt-1 max-w-xs text-xs text-muted-foreground"></p>
                                            </td>
                                            <td class="align-middle px-4 py-3">
                                                <template x-if="bed.is_occupied">
                                                    <div>
                                                        <p class="font-medium" x-text="bed.occupant_name"></p>
                                                        <p class="text-xs text-muted-foreground" x-text="bed.occupant_admission_number"></p>
                                                    </div>
                                                </template>
                                                <span x-show="!bed.is_occupied" class="text-muted-foreground">—</span>
                                            </td>
                                            @if ($canManage)
                                                <td class="align-middle px-4 py-3">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <button type="button" @click="editingBedId = editingBedId === bed.id ? null : bed.id; leavingBedId = null"
                                                            class="inline-flex h-8 items-center rounded-md border border-input bg-background px-2.5 text-xs font-medium shadow-sm hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                            Edit
                                                        </button>
                                                        <button type="button" x-show="bed.is_occupied" @click="leavingBedId = leavingBedId === bed.id ? null : bed.id; editingBedId = null"
                                                            class="inline-flex h-8 items-center rounded-md border border-input bg-background px-2.5 text-xs font-medium shadow-sm hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                            End placement
                                                        </button>
                                                    </div>

                                                    <form x-show="editingBedId === bed.id" x-cloak :action="bed.update_url" method="POST" class="mt-3 min-w-[220px] space-y-2 rounded-md bg-muted/20 p-3">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="PUT">
                                                        <label class="sr-only" :for="'modal-bed-name-' + bed.id">Bed name</label>
                                                        <input :id="'modal-bed-name-' + bed.id" name="name" required maxlength="40" :value="bed.name"
                                                            class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                        <label class="sr-only" :for="'modal-bed-status-' + bed.id">Bed status</label>
                                                        <select :id="'modal-bed-status-' + bed.id" name="status" required :value="bed.status"
                                                            class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                            <option value="available">Available</option>
                                                            <option value="maintenance">Maintenance</option>
                                                            <option value="unavailable">Unavailable</option>
                                                            <option value="retired">Retired</option>
                                                        </select>
                                                        <label class="sr-only" :for="'modal-bed-reason-' + bed.id">Status reason</label>
                                                        <input :id="'modal-bed-reason-' + bed.id" name="status_reason" maxlength="1000" :value="bed.status_reason" placeholder="Reason (optional)"
                                                            class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                        <button type="submit" class="inline-flex h-8 items-center rounded-md bg-primary px-3 text-xs font-medium text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">Save bed</button>
                                                    </form>

                                                    <form x-show="leavingBedId === bed.id" x-cloak :action="bed.leave_url" method="POST" class="mt-3 min-w-[220px] space-y-2 rounded-md bg-muted/20 p-3">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <label class="sr-only" :for="'modal-leave-reason-' + bed.id">Reason for ending placement</label>
                                                        <input :id="'modal-leave-reason-' + bed.id" name="reason" required maxlength="255" placeholder="Why are they leaving?"
                                                            class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                        <button type="submit" class="inline-flex h-8 items-center rounded-md border border-input bg-background px-3 text-xs font-medium hover:bg-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">End placement</button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <p x-show="selectedRoom.beds.length === 0" x-cloak class="px-4 py-6 text-center text-sm text-muted-foreground">This room has no beds yet.</p>
                    </div>

                    @if ($canManage)
                        <form x-show="selectedRoom.is_active" :action="selectedRoom.add_bed_url" method="POST" class="flex flex-wrap items-end gap-2 rounded-lg border border-dashed p-4">
                            @csrf
                            <div class="flex min-w-52 flex-1 flex-col gap-1">
                                <label for="modal-new-bed" class="text-xs font-medium">Add a bed</label>
                                <input id="modal-new-bed" name="name" required maxlength="40" :placeholder="'Bed ' + (selectedRoom.bed_count + 1)"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            </div>
                            <button type="submit" class="inline-flex h-9 items-center rounded-md bg-primary px-3 text-sm font-medium text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">Add bed</button>
                        </form>
                        <p x-show="!selectedRoom.is_active" x-cloak class="text-sm text-muted-foreground">Activate this room before adding beds.</p>
                    @endif
                </div>
            </template>
        </slot:content>
    </april:dialog>

    @if ($canManage && $dormitory->is_active && $assignableBeds->isNotEmpty())
        <form action="{{ route('boarding-places.store') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Give a learner a bed</h3>
                    <p class="text-sm text-muted-foreground">Only free, active beds marked Available are offered. Moving a learner keeps the old record.</p>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="place-learner" class="text-sm font-medium leading-none">Learner</label>
                        <select id="place-learner" name="student_record_id" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($learners as $learner)
                                <option value="{{ $learner->id }}">{{ $learner->user?->name }} &middot; {{ $learner->admission_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="place-bed" class="text-sm font-medium leading-none">Bed</label>
                        <select id="place-bed" name="dormitory_bed_id" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($assignableBeds as $bed)
                                <option value="{{ $bed->id }}">{{ $bed->room->name }} &middot; {{ $bed->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="place-reason" class="text-sm font-medium leading-none">Note <span class="font-normal text-muted-foreground">(optional)</span></label>
                        <input id="place-reason" name="reason" maxlength="255" value="{{ old('reason') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                </div>

                <div class="flex justify-end border-t p-6">
                    <april:button type="submit">
                        <x-lucide-check class="mr-2 size-4" />
                        Give the bed
                    </april:button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
