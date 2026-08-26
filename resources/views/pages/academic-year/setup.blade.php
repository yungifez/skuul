@php
    $stepItems = collect($progress['steps'])->map(function (array $step) use ($academicYear, $currentStep): array {
        $state = $step['value'] === $currentStep->value ? 'current' : ($step['complete'] ? 'complete' : 'upcoming');

        return $step + [
            'state' => $state,
            'href' => $state === 'complete' ? route('academic-years.setup', [$academicYear, $step['value']]) : null,
        ];
    })->all();
@endphp

@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-years.index'), 'text' => school_terms('academic_year', 'School years')],
    ['href' => route('academic-years.show', $academicYear), 'text' => $academicYear->name],
    ['href' => route('academic-years.setup', [$academicYear, $currentStep->value]), 'text' => 'Setup', 'active'],
]])

@section('title', 'Set up '.$academicYear->name)
@section('page_heading', 'Set up '.$academicYear->name)

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
        <div class="flex items-center gap-1 text-sm text-muted-foreground">
            <span>Setup saves automatically.</span>
            <x-help-tooltip label="Academic year setup help">You can leave this page and continue later. Completed steps stay available for review.</x-help-tooltip>
        </div>
        <april:steps :items="$stepItems" :current="$currentStep->value" />

        @if ($currentStep === \App\Enums\AcademicYearSetupStep::Calendar)
            @livewire('academic-calendar-form', ['academicYear' => $academicYear, 'setupWizard' => true])
        @elseif ($currentStep === \App\Enums\AcademicYearSetupStep::Teaching)
            <april:card>
                <slot:title>Choose the teaching approach</slot:title>
                <slot:description>Set the default grouping for subjects.</slot:description>
                <slot:footer><x-help-tooltip label="Teaching approach help">This sets the default grouping for subjects in {{ $academicYear->name }}. A subject can still use an exception later.</x-help-tooltip></slot:footer>
                <slot:content>
                    <april:button-link href="{{ route('academic-years.instructional-model.edit', [$academicYear, 'setup' => 1]) }}">Set teaching approach</april:button-link>
                </slot:content>
            </april:card>
        @elseif ($currentStep === \App\Enums\AcademicYearSetupStep::Structure)
            <april:card>
                <slot:title>Build this year’s classes</slot:title>
                <slot:description>Create the classes and groups used this year.</slot:description>
                <slot:footer><x-help-tooltip label="Class setup help">Add reusable classes or grades first. Then create this year’s sections or forms and assign class teachers.</x-help-tooltip></slot:footer>
                <slot:content class="flex flex-wrap gap-3">
                    <april:button-link href="{{ route('academic-levels.create', ['setup' => 1, 'academic_year_id' => $academicYear->id]) }}">Add a class or grade</april:button-link>
                    <april:button-link href="{{ route('academic-levels.index') }}" variant="ghost">Manage classes or grades</april:button-link>
                    <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_year_id' => $academicYear->id, 'setup' => 1]) }}" variant="outline">Add this year’s first class</april:button-link>
                    <april:button-link href="{{ route('academic-cycle-sections.index', ['academic_year_id' => $academicYear->id]) }}" variant="ghost">Manage sections</april:button-link>
                </slot:content>
            </april:card>
        @elseif ($currentStep === \App\Enums\AcademicYearSetupStep::Subjects)
            <april:card>
                <slot:title>Choose the subjects being taught</slot:title>
                <slot:description>Add subjects and choose when they run.</slot:description>
                <slot:footer><x-help-tooltip label="Subject setup help">Add a subject for a class, then choose every term or semester when it runs. Exam slots are not needed for this setup.</x-help-tooltip></slot:footer>
                <slot:content class="flex flex-wrap gap-3">
                    <april:button-link href="{{ route('course-offerings.create', ['academic_year_id' => $academicYear->id, 'setup' => 1]) }}">Add subjects for this year</april:button-link>
                    <april:button-link href="{{ route('subjects.create', ['setup' => 1, 'academic_year_id' => $academicYear->id]) }}" variant="outline">Create a new subject</april:button-link>
                </slot:content>
            </april:card>
        @else
            <april:card>
                <slot:title>Review before publishing</slot:title>
                <slot:description>Check the calendar, then publish it.</slot:description>
                <slot:footer><x-help-tooltip label="Publishing help">Publishing makes the calendar available. Classes and subjects can continue to be configured afterwards.</x-help-tooltip></slot:footer>
                <slot:content>
                    @if ($errors->has('setup'))
                        <div class="mb-4 rounded-md border border-destructive/30 bg-destructive/10 p-3 text-sm text-destructive">{{ $errors->first('setup') }}</div>
                    @endif
                    <dl class="grid gap-3 sm:grid-cols-2">
                        <div><dt class="text-sm text-muted-foreground">Dates</dt><dd class="font-medium">{{ $academicYear->starts_on?->format('M j, Y') }} – {{ $academicYear->ends_on?->format('M j, Y') }}</dd></div>
                        <div><dt class="text-sm text-muted-foreground">Reporting periods</dt><dd class="font-medium">{{ $academicYear->topLevelPeriods->count() }}</dd></div>
                    </dl>
                </slot:content>
                <slot:footer>
                    <form method="POST" action="{{ route('academic-years.setup.publish', $academicYear) }}">
                        @csrf
                        <april:button type="submit">Publish and finish setup</april:button>
                    </form>
                </slot:footer>
            </april:card>
        @endif

        <div class="flex justify-between">
            @if (($previous = $currentStep->previous()) !== null)
                <april:button-link href="{{ route('academic-years.setup', [$academicYear, $previous->value]) }}" variant="outline">Back</april:button-link>
            @else
                <span></span>
            @endif
            <april:button-link href="{{ route('academic-years.show', $academicYear) }}" variant="ghost">Save and finish later</april:button-link>
        </div>
    </div>
@endsection
