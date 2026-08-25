@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('library-copies.index'), 'text' => 'Library'],
    ['href' => route('library-loans.index'), 'text' => 'Lending desk', 'active'],
]])

@section('title', __('Lending desk'))

@section('page_heading', __('Lending desk'))

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Lending desk</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            This library lends for {{ $rules->loan_days }} days.
            @if ($rules->chargesFines())
                A late book costs {{ $rules->dailyFine()->formatToLocale(app()->getLocale()) }} a day, charged to the learner's account.
            @else
                Late books carry no fine.
            @endif
        </p>
    </div>

    <x-display-validation-errors />

    @if ($canLend)
        <form action="{{ route('library-loans.store') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Lend a copy</h3>
                    <p class="text-sm text-muted-foreground">Scan or type the barcode, then say who is taking it.</p>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="loan-barcode" class="text-sm font-medium leading-none">Barcode</label>
                        <input id="loan-barcode" name="barcode" required maxlength="60" autofocus autocomplete="off"
                            value="{{ old('barcode') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 font-mono text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="loan-borrower" class="text-sm font-medium leading-none">Who is taking it</label>
                        <select id="loan-borrower" name="user_id" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($borrowers as $borrower)
                                <option value="{{ $borrower->id }}">{{ $borrower->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end border-t p-6">
                    <april:button type="submit">
                        <x-lucide-book-open-check class="mr-2 size-4" />
                        Lend it
                    </april:button>
                </div>
            </div>
        </form>

        <form action="{{ route('library-loans.section.store') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Lend a class set</h3>
                    <p class="text-sm text-muted-foreground">Give one title to every attending learner in a {{ strtolower(school_term('section', 'section')) }}. The whole set succeeds or nothing changes.</p>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="section-loan-section" class="text-sm font-medium leading-none">Section</label>
                        <select id="section-loan-section" name="academic_cycle_section_id" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->academicYear->name }} · {{ $section->academicLevel->name }} · {{ $section->label ?? $section->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="section-loan-title" class="text-sm font-medium leading-none">Title</label>
                        <select id="section-loan-title" name="library_title_id" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            @foreach ($titles as $title)
                                <option value="{{ $title->id }}">{{ $title->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end border-t p-6">
                    <april:button type="submit">Lend the class set</april:button>
                </div>
            </div>
        </form>
    @endif

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Out now</h3>
            <p class="text-sm text-muted-foreground">
                {{ $open->count() }} copies are out, and {{ $overdue }} of them should already be back.
            </p>
        </div>

        @if ($open->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-library class="size-6" />
                </span>
                <p class="text-sm font-medium">Everything is on the shelf.</p>
            </div>
        @else
            <ul class="divide-y">
                @foreach ($open as $loan)
                    @php ($late = $loan->daysLate())
                    <li class="flex flex-col gap-3 p-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium">{{ $loan->copy?->title?->title }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $loan->borrower?->name }} &middot;
                                <span class="font-mono text-xs">{{ $loan->copy?->barcode }}</span> &middot;
                                due {{ $loan->due_on?->format('j M Y') }}
                            </p>
                            @if ($late > 0)
                                <april:badge variant="destructive" class="mt-2">{{ $late }} days late</april:badge>
                            @endif
                        </div>

                        @if ($canLend)
                            <div class="flex gap-2">
                                <form action="{{ route('library-loans.update', $loan->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="do" value="return">
                                    <april:button type="submit" size="sm">Take it back</april:button>
                                </form>
                                @if ($late === 0 && $loan->renewals < $rules->renewals_allowed)
                                    <form action="{{ route('library-loans.update', $loan->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="do" value="renew">
                                        <april:button type="submit" variant="outline" size="sm">Renew</april:button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Recently back</h3>
        </div>

        @if ($returned->isEmpty())
            <div class="p-10 text-center text-sm text-muted-foreground">Nothing has come back yet.</div>
        @else
            <ul class="divide-y">
                @foreach ($returned as $loan)
                    <li class="flex flex-col gap-1 p-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium">{{ $loan->copy?->title?->title }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $loan->borrower?->name }} &middot; back {{ $loan->returned_on?->format('j M Y') }}
                            </p>
                        </div>
                        @if ($loan->fine_charged > 0)
                            <april:badge variant="outline">Fine {{ $loan->fine()->formatToLocale(app()->getLocale()) }}</april:badge>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
