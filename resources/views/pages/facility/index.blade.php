@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('facilities.index'), 'text' => 'Facilities', 'active'],
]])

@section('title', __('Facilities'))

@section('page_heading', __('Facilities'))

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">What the campus shares</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            Halls, laboratories, vehicles, and kit that more than one class wants. A lesson can be moved into one of
            these for a single entry, and the timetable refuses to publish two {{ strtolower(school_terms('class_level', 'classes')) }} into the same place at once.
        </p>
    </div>

    <x-display-validation-errors />

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">The catalogue</h3>
        </div>

        @if ($facilities->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-building-2 class="size-6" />
                </span>
                <p class="text-sm font-medium">Nothing is shared yet.</p>
                <p class="max-w-sm text-sm text-muted-foreground">
                    Until something is listed here, a lesson can only happen in the section's own room.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 font-medium">Name</th>
                            <th class="p-4 font-medium">Kind</th>
                            <th class="p-4 font-medium">Holds</th>
                            <th class="p-4 font-medium">Booked ahead</th>
                            <th class="p-4 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facilities as $facility)
                            <tr class="border-b last:border-0 {{ $facility->is_active ? '' : 'text-muted-foreground' }}">
                                <td class="p-4 font-medium">
                                    {{ $facility->name }}
                                    @if (!$facility->is_active)
                                        <april:badge variant="secondary" class="ml-2">Out of use</april:badge>
                                    @endif
                                </td>
                                <td class="p-4 text-muted-foreground">{{ $facility->kind->label() }}</td>
                                <td class="p-4 text-muted-foreground">{{ $facility->capacity ?? '—' }}</td>
                                <td class="p-4 text-muted-foreground">{{ $facility->upcoming_bookings_count }}</td>
                                <td class="p-4 text-right">
                                    @if ($canManage && $facility->is_active)
                                        <form action="{{ route('facilities.destroy', $facility->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <april:button type="submit" variant="ghost" size="sm">Take out of use</april:button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Booked next</h3>
            <p class="text-sm text-muted-foreground">Everything claimed from now on.</p>
        </div>

        @if ($bookings->isEmpty())
            <div class="p-10 text-center text-sm text-muted-foreground">Nothing is booked.</div>
        @else
            <ul class="divide-y">
                @foreach ($bookings as $booking)
                    <li class="flex flex-col gap-2 p-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium">{{ $booking->facility?->name }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $booking->starts_at?->format('j M Y, H:i') }} to {{ $booking->ends_at?->format('H:i') }}
                                &middot; {{ $booking->purpose }}
                                @if ($booking->bookedBy !== null)
                                    &middot; {{ $booking->bookedBy->name }}
                                @endif
                            </p>
                        </div>

                        @if ($canBook)
                            <form action="{{ route('facilities.bookings.cancel', $booking->id) }}" method="POST" class="flex gap-2">
                                @csrf
                                @method('DELETE')
                                <input name="reason" maxlength="255" placeholder="Why give it up?"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring sm:w-56">
                                <april:button type="submit" variant="outline" size="sm">Give it up</april:button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($canBook && $facilities->isNotEmpty())
        <form action="{{ route('facilities.book') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Book something</h3>
                    <p class="text-sm text-muted-foreground">
                        A booking is refused when somebody else already has it, or when a published lesson is scheduled
                        there at that time.
                    </p>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="booking-facility" class="text-sm font-medium leading-none">What</label>
                        <select id="booking-facility" name="facility_id" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($facilities->where('is_active', true) as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->name }} &middot; {{ $facility->kind->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="booking-from" class="text-sm font-medium leading-none">From</label>
                        <input id="booking-from" name="starts_at" type="datetime-local" required value="{{ old('starts_at') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="booking-to" class="text-sm font-medium leading-none">Until</label>
                        <input id="booking-to" name="ends_at" type="datetime-local" required value="{{ old('ends_at') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="booking-purpose" class="text-sm font-medium leading-none">What for</label>
                        <input id="booking-purpose" name="purpose" required maxlength="255" value="{{ old('purpose') }}"
                            placeholder="Speech day rehearsal"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                </div>

                <div class="flex justify-end border-t p-6">
                    <april:button type="submit">
                        <x-lucide-calendar-check class="mr-2 size-4" />
                        Book it
                    </april:button>
                </div>
            </div>
        </form>
    @endif

    @if ($canManage)
        <form action="{{ route('facilities.store') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Share something new</h3>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="facility-name" class="text-sm font-medium leading-none">Name</label>
                        <input id="facility-name" name="name" required maxlength="120" value="{{ old('name') }}"
                            placeholder="Main hall"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="facility-kind" class="text-sm font-medium leading-none">Kind</label>
                        <select id="facility-kind" name="kind" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($kinds as $kind)
                                <option value="{{ $kind->value }}">{{ $kind->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="facility-capacity" class="text-sm font-medium leading-none">How many it holds <span class="font-normal text-muted-foreground">(optional)</span></label>
                        <input id="facility-capacity" name="capacity" type="number" min="1" value="{{ old('capacity') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="facility-notes" class="text-sm font-medium leading-none">Notes <span class="font-normal text-muted-foreground">(optional)</span></label>
                        <input id="facility-notes" name="notes" maxlength="1000" value="{{ old('notes') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                </div>

                <div class="flex justify-end border-t p-6">
                    <april:button type="submit">
                        <x-lucide-check class="mr-2 size-4" />
                        Add it
                    </april:button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
