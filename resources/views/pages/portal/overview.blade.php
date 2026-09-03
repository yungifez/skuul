@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school', 'active'],
]])

@section('title', __('My school'))

@section('page_heading', __('My school'))

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Everything in one place</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            Each campus keeps its own records, so every entry below says which campus it belongs to. This page only
            shows what is there. Asking the school for something happens on that campus's own page.
        </p>
    </div>

    @foreach ($campuses as $campusName => $enrollments)
        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">{{ $campusName }}</h3>
                <p class="text-sm text-muted-foreground">
                    {{ trans_choice(':count enrolment|:count enrolments', $enrollments->count(), ['count' => $enrollments->count()]) }}
                    at this campus.
                </p>
            </div>

            <div class="flex flex-col divide-y">
                @foreach ($enrollments as $enrollment)
                    <div class="flex flex-col gap-3 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-medium">{{ $enrollment->user?->name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ $enrollment->admission_number }} &middot; {{ $enrollment->status->label() }}
                                </p>
                            </div>
                            <april:badge variant="secondary">{{ $campusName }}</april:badge>
                        </div>

                        @if ($areasOf[$enrollment->id] === [])
                            <p class="text-sm text-muted-foreground">
                                This campus has closed the family pages. Ask the school office for anything you need.
                            </p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($areasOf[$enrollment->id] as $area)
                                    @php($route = match ($area) {
                                        \App\Enums\PortalArea::Attendance => route('portal.attendance.show', $enrollment),
                                        \App\Enums\PortalArea::Notices => route('portal.notices.index', $enrollment),
                                        \App\Enums\PortalArea::Calendar => route('portal.calendar.index', $enrollment),
                                        \App\Enums\PortalArea::Invoices => route('portal.invoices.index', $enrollment),
                                        \App\Enums\PortalArea::Documents => route('portal.documents.index', $enrollment),
                                        \App\Enums\PortalArea::Boarding => route('portal.boarding.index', $enrollment),
                                        \App\Enums\PortalArea::Library => route('portal.library.index', $enrollment),
                                        \App\Enums\PortalArea::Requests => route('portal.requests.index', $enrollment),
                                        default => null,
                                    })
                                    @if ($route !== null)
                                        <april:button-link href="{{ $route }}" variant="outline" size="sm">
                                            {{ $area->label() }}
                                        </april:button-link>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
