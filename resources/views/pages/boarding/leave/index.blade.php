@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('overnight-leaves.index'), 'text' => 'Nights away', 'active'],
]])

@section('title', __('Nights away'))

@section('page_heading', __('Nights away'))

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Nights away</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            Who is not in the building tonight, and who agreed to it.
        </p>
    </div>

    <x-display-validation-errors />

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Out tonight</h3>
            <p class="text-sm text-muted-foreground">Read this at lights out.</p>
        </div>

        @if ($tonight->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-moon class="size-6" />
                </span>
                <p class="text-sm font-medium">Everybody is in the house.</p>
            </div>
        @else
            <ul class="divide-y">
                @foreach ($tonight as $leave)
                    <li class="flex flex-col gap-2 p-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium">{{ $leave->studentRecord?->user?->name }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $leave->destination }}
                                @if ($leave->contact)
                                    &middot; {{ $leave->contact }}
                                @endif
                                &middot; back {{ $leave->returns_on?->format('j M Y') }}
                            </p>
                        </div>

                        @if ($canDecide)
                            <form action="{{ route('overnight-leaves.update', $leave->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="returned">
                                <april:button type="submit" variant="outline" size="sm">Back in the house</april:button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Waiting for a decision</h3>
        </div>

        @if ($waiting->isEmpty())
            <div class="p-10 text-center text-sm text-muted-foreground">Nothing is waiting.</div>
        @else
            <ul class="divide-y">
                @foreach ($waiting as $leave)
                    <li class="flex flex-col gap-3 p-6 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-medium">{{ $leave->studentRecord?->user?->name }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $leave->leaves_on?->format('j M') }} to {{ $leave->returns_on?->format('j M Y') }} &middot; {{ $leave->destination }}
                            </p>
                            @if ($leave->reason)
                                <p class="mt-1 text-sm text-muted-foreground">{{ $leave->reason }}</p>
                            @endif
                        </div>

                        @if ($canDecide)
                            <div class="flex gap-2">
                                <form action="{{ route('overnight-leaves.update', $leave->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <april:button type="submit" size="sm">Approve</april:button>
                                </form>
                                <form action="{{ route('overnight-leaves.update', $leave->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="refused">
                                    <april:button type="submit" variant="outline" size="sm">Refuse</april:button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($canAsk)
        <form action="{{ route('overnight-leaves.store') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Ask for a night away</h3>
                    <p class="text-sm text-muted-foreground">Only a learner who boards can leave the house.</p>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="leave-learner" class="text-sm font-medium leading-none">Learner</label>
                        <select id="leave-learner" name="student_record_id" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($learners as $learner)
                                <option value="{{ $learner->id }}">{{ $learner->user?->name }} &middot; {{ $learner->admission_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="leave-from" class="text-sm font-medium leading-none">Leaves on</label>
                        <input id="leave-from" name="leaves_on" type="date" required value="{{ old('leaves_on', now()->toDateString()) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="leave-to" class="text-sm font-medium leading-none">Comes back on</label>
                        <input id="leave-to" name="returns_on" type="date" required value="{{ old('returns_on', now()->addDay()->toDateString()) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="leave-destination" class="text-sm font-medium leading-none">Where are they going?</label>
                        <input id="leave-destination" name="destination" required maxlength="150" value="{{ old('destination') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="leave-contact" class="text-sm font-medium leading-none">Who can the house ring? <span class="font-normal text-muted-foreground">(optional)</span></label>
                        <input id="leave-contact" name="contact" maxlength="100" value="{{ old('contact') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="leave-reason" class="text-sm font-medium leading-none">Reason <span class="font-normal text-muted-foreground">(optional)</span></label>
                        <input id="leave-reason" name="reason" maxlength="1000" value="{{ old('reason') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                </div>

                <div class="flex justify-end border-t p-6">
                    <april:button type="submit">
                        <x-lucide-check class="mr-2 size-4" />
                        Ask for the night away
                    </april:button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
