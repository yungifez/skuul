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
<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
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
                        <th scope="col" class="px-6 py-3 font-medium">Beds</th>
                        <th scope="col" class="px-6 py-3 font-medium">Status</th>
                        @if ($canManage)
                            <th scope="col" class="px-6 py-3 font-medium">Edit</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($dormitory->rooms as $room)
                        <tr class="align-top {{ $room->is_active ? '' : 'text-muted-foreground' }}">
                            <td class="px-6 py-4 font-medium text-foreground">{{ $room->name }}</td>
                            <td class="px-6 py-4">{{ $room->floor ?: '—' }}</td>
                            <td class="px-6 py-4">{{ $room->beds->count() }}</td>
                            <td class="px-6 py-4">
                                @if ($room->is_active)
                                    <april:badge variant="secondary">In use</april:badge>
                                @else
                                    <april:badge variant="outline">Out of use</april:badge>
                                @endif
                            </td>
                            @if ($canManage)
                                <td class="px-6 py-4">
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
                            <td colspan="{{ $canManage ? 5 : 4 }}" class="px-6 py-8 text-center text-sm text-muted-foreground">This house has no rooms yet.</td>
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

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Beds</h3>
            <p class="text-sm text-muted-foreground">Availability is explicit. Occupied is derived from the current boarding record.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1050px] text-left text-sm">
                <thead class="border-b bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">Room</th>
                        <th scope="col" class="px-6 py-3 font-medium">Bed</th>
                        <th scope="col" class="px-6 py-3 font-medium">Status</th>
                        <th scope="col" class="px-6 py-3 font-medium">Occupant</th>
                        @if ($canManage)
                            <th scope="col" class="px-6 py-3 font-medium">Edit</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($dormitory->rooms as $room)
                        @forelse ($room->beds as $bed)
                            @php
                                $place = $occupiedBy->get($bed->id);
                                $status = $bed->status;
                            @endphp
                            <tr class="align-top {{ $room->is_active && $bed->is_active ? '' : 'text-muted-foreground' }}">
                                <td class="px-6 py-4 font-medium text-foreground">{{ $room->name }}</td>
                                <td class="px-6 py-4 font-medium text-foreground">{{ $bed->name }}</td>
                                <td class="px-6 py-4">
                                    @if ($place !== null)
                                        <april:badge variant="secondary">Occupied</april:badge>
                                    @else
                                        <april:badge variant="outline">{{ $status->label() }}</april:badge>
                                        @if ($bed->status_reason)
                                            <p class="mt-1 max-w-xs text-xs">{{ $bed->status_reason }}</p>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($place)
                                        <p class="font-medium text-foreground">{{ $place->studentRecord?->user?->name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ $place->studentRecord?->admission_number }}</p>
                                        @if ($canManage)
                                            <form action="{{ route('boarding-places.destroy', $place->student_record_id) }}" method="POST" class="mt-2 flex min-w-56 flex-col gap-2">
                                                @csrf
                                                @method('DELETE')
                                                <input name="reason" required maxlength="255" placeholder="Why are they leaving?"
                                                    class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                <april:button type="submit" variant="ghost" size="sm">Leave bed</april:button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-muted-foreground">—</span>
                                    @endif
                                </td>
                                @if ($canManage)
                                    <td class="px-6 py-4">
                                        <form action="{{ route('dormitory-beds.update', $bed->id) }}" method="POST" class="flex min-w-52 flex-col gap-2">
                                            @csrf
                                            @method('PUT')
                                            <label for="bed-name-{{ $bed->id }}" class="sr-only">Bed name</label>
                                            <input id="bed-name-{{ $bed->id }}" name="name" required maxlength="40" value="{{ $bed->name }}"
                                                class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                            <label for="bed-status-{{ $bed->id }}" class="sr-only">Bed status</label>
                                            <select id="bed-status-{{ $bed->id }}" name="status" required
                                                class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                @foreach (\App\Enums\DormitoryBedStatus::cases() as $bedStatus)
                                                    <option value="{{ $bedStatus->value }}" @selected($status === $bedStatus)>{{ $bedStatus->label() }}</option>
                                                @endforeach
                                            </select>
                                            <label for="bed-reason-{{ $bed->id }}" class="sr-only">Status reason</label>
                                            <input id="bed-reason-{{ $bed->id }}" name="status_reason" maxlength="1000" value="{{ $bed->status_reason }}" placeholder="Reason (optional)"
                                                class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                            <april:button type="submit" variant="outline" size="sm">Save bed</april:button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManage ? 5 : 4 }}" class="px-6 py-4 text-sm text-muted-foreground">{{ $room->name }} has no beds.</td>
                            </tr>
                        @endforelse
                        @if ($canManage && $room->is_active)
                            <tr class="bg-muted/20">
                                <td colspan="{{ $canManage ? 5 : 4 }}" class="px-6 py-4">
                                    <form action="{{ route('dormitory-beds.store', $room->id) }}" method="POST" class="flex flex-wrap items-end gap-2">
                                        @csrf
                                        <div class="flex flex-col gap-1">
                                            <label for="new-bed-{{ $room->id }}" class="text-xs font-medium">Add a bed to {{ $room->name }}</label>
                                            <input id="new-bed-{{ $room->id }}" name="name" required maxlength="40" placeholder="Bed {{ $room->beds->count() + 1 }}"
                                                class="flex h-8 w-48 rounded-md border border-input bg-background px-2 text-xs ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                        </div>
                                        <april:button type="submit" variant="outline" size="sm">Add bed</april:button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 5 : 4 }}" class="px-6 py-8 text-center text-sm text-muted-foreground">This house has no beds yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
