@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => school_terms('course', 'Course').' being taught'],
    ['href' => route('course-offerings.gradebook.show', $courseOffering), 'text' => 'Gradebook', 'active'],
]])

@section('title', 'Gradebook · '.$courseOffering->subject->name)
@section('page_heading', 'Gradebook')

@section('page_actions')
    <april:button-link href="{{ route('course-offerings.index') }}" variant="outline">Back to {{ school_terms('course', 'courses') }}</april:button-link>
@endsection

@section('content')
    @php
        $periodStatus = $courseOffering->academicPeriod?->status;
        $gradebookAcceptsWrites = $periodStatus?->acceptsWrites() ?? false;
        $gradebookAcceptsNewWork = $periodStatus?->acceptsNewWork() ?? false;
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-3 border-b pb-4">
            <div class="min-w-0">
                <p class="font-semibold">{{ $courseOffering->subject->name }} <span class="font-normal text-muted-foreground">· {{ $courseOffering->academicLevel->name }}</span></p>
                <p class="text-sm text-muted-foreground">
                    {{ $courseOffering->academicYear->name }} · {{ $courseOffering->academicPeriod->display_name }}
                    · {{ school_roster_label($courseOffering->roster_mode) }}
                    · {{ $learnerCount }} {{ Str::plural('learner', $learnerCount) }}
                </p>
            </div>
            <span class="rounded-full border px-2.5 py-1 text-xs {{ $gradebookAcceptsNewWork ? 'border-primary/30 bg-primary text-primary-foreground' : ($gradebookAcceptsWrites ? 'border-blue-500/30 bg-blue-500/10 text-blue-700 dark:text-blue-300' : 'border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-300') }}">
                {{ $gradebookAcceptsNewWork ? 'Editing open' : ($gradebookAcceptsWrites ? 'Corrections open' : 'Read-only') }}
            </span>
        </div>

        @if (!$gradebookAcceptsWrites)
            <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm">
                <p class="font-medium text-amber-900 dark:text-amber-100">This gradebook is read-only.</p>
                <p class="mt-1 text-amber-800 dark:text-amber-200">The {{ $courseOffering->academicPeriod->display_name }} period is {{ strtolower($courseOffering->academicPeriod->status->label()) }}. Existing marks and published results remain available, but assessment setup and mark entry are locked.</p>
            </div>
        @endif

        @if ($gradebookAcceptsNewWork)
            @can('manageGradebook', $courseOffering)
                <livewire:gradebook-setup :course-offering="$courseOffering" />
            @endcan
        @endif

        <livewire:gradebook-mark-sheet :course-offering="$courseOffering" />
    </div>
@endsection
