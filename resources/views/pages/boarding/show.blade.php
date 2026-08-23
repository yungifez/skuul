@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('dormitories.show', $dormitory->id), 'text' => $dormitory->name, 'active'],
]])

@section('title', $dormitory->name)

@section('page_heading', $dormitory->name)

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ $dormitory->label }}</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-foreground md:text-3xl">{{ $dormitory->name }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            {{ $occupancy['taken'] }} of {{ $occupancy['beds'] }} beds are taken, and {{ $occupancy['away'] }} learners are out tonight.
        </p>
    </div>

    <x-display-validation-errors />

    @if ($onDuty->isNotEmpty())
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">On duty</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($onDuty as $duty)
                    <april:badge variant="outline">{{ $duty->user?->name }} &middot; {{ $duty->role->label() }}</april:badge>
                @endforeach
            </div>
        </div>
    @endif

    @if ($away->isNotEmpty())
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Out of the house tonight</p>
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
            <h3 class="text-lg font-semibold leading-none tracking-tight">Who sleeps where</h3>
            <p class="text-sm text-muted-foreground">Every bed in the house, and the learner in it.</p>
        </div>

        <div class="flex flex-col gap-6 p-6">
            @foreach ($dormitory->rooms as $room)
                <div>
                    <p class="text-sm font-semibold">{{ $room->name }}</p>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($room->beds as $bed)
                            @php ($place = $occupiedBy->get($bed->id))
                            <div class="rounded-lg border p-3 text-sm {{ $place === null ? 'border-dashed text-muted-foreground' : 'border-input' }}">
                                <p class="text-xs uppercase tracking-wider text-muted-foreground">{{ $bed->name }}</p>
                                @if ($place === null)
                                    <p class="mt-1">Free</p>
                                @else
                                    <p class="mt-1 font-medium text-foreground">{{ $place->studentRecord?->user?->name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $place->studentRecord?->admission_number }}</p>
                                    @if ($canManage)
                                        <form action="{{ route('boarding-places.destroy', $place->student_record_id) }}" method="POST" class="mt-2 flex gap-2">
                                            @csrf
                                            @method('DELETE')
                                            <input name="reason" required maxlength="255" placeholder="Why are they leaving?"
                                                class="flex h-8 w-full rounded-md border border-input bg-background px-2 text-xs ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                            <april:button type="submit" variant="ghost" size="sm">Leave</april:button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if ($canManage)
        <form action="{{ route('boarding-places.store') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Give a learner a bed</h3>
                    <p class="text-sm text-muted-foreground">
                        Only free beds are offered. Moving a learner keeps the old record, so the house can say where a child
                        slept last term.
                    </p>
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
                            @foreach ($dormitory->rooms as $room)
                                @foreach ($room->beds as $bed)
                                    @if ($occupiedBy->get($bed->id) === null)
                                        <option value="{{ $bed->id }}">{{ $room->name }} &middot; {{ $bed->name }}</option>
                                    @endif
                                @endforeach
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
