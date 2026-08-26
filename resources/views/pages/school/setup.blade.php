@php
    $stepItems = collect($progress['steps'])->map(function (array $step) use ($school, $currentStep): array {
        $state = $step['value'] === $currentStep->value ? 'current' : ($step['complete'] ? 'complete' : 'upcoming');

        return $step + [
            'state' => $state,
            'href' => $state === 'complete' ? route('schools.setup', [$school, $step['value']]) : null,
        ];
    })->all();
@endphp

@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup'],
    ['href' => route('schools.setup', [$school, $currentStep->value]), 'text' => 'Quick setup', 'active'],
]])

@section('title', 'Set up '.$school->name)
@section('page_heading', 'Set up '.$school->name)

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
        <div>
            <div class="flex items-center gap-1 text-sm text-muted-foreground">
                <span>Complete the school setup steps in order.</span>
                <x-help-tooltip label="Quick school setup help">You can leave and continue later. Completed steps stay available for review.</x-help-tooltip>
            </div>
        </div>
        <april:steps :items="$stepItems" :current="$currentStep->value" />

        @if ($currentStep === \App\Enums\SchoolSetupStep::Details)
            <april:card>
                <slot:title>Confirm school details</slot:title>
                <slot:description>Review the name, address, and contact details.</slot:description>
                <slot:footer><x-help-tooltip label="School details setup help">These details appear to staff, families, and on printed records.</x-help-tooltip></slot:footer>
                <slot:content><april:button-link href="{{ route('schools.edit', [$school, 'setup' => 1]) }}">Review school details</april:button-link></slot:content>
            </april:card>
        @elseif ($currentStep === \App\Enums\SchoolSetupStep::Language)
            <april:card>
                <slot:title>Choose the school’s language</slot:title>
                <slot:description>Choose familiar labels for your school.</slot:description>
                <slot:footer><x-help-tooltip label="School language setup help">Use labels such as class teacher or form teacher, grade or class, and term or semester.</x-help-tooltip></slot:footer>
                <slot:content><april:button-link href="{{ route('schools.operating-profile.edit', ['setup' => 1]) }}">Set school language</april:button-link></slot:content>
            </april:card>
        @elseif ($currentStep === \App\Enums\SchoolSetupStep::Classes)
            <april:card>
                <slot:title>Add classes or grades</slot:title>
                <slot:description>Add the reusable levels your school teaches.</slot:description>
                <slot:footer><x-help-tooltip label="Class setup help">These levels can be reused each year. You will choose which ones run in each academic year next.</x-help-tooltip></slot:footer>
                <slot:content><april:button-link href="{{ route('academic-levels.create', ['setup' => 1]) }}">Add a class or grade</april:button-link></slot:content>
            </april:card>
        @elseif ($currentStep === \App\Enums\SchoolSetupStep::AcademicYear)
            <april:card>
                <slot:title>Create the first academic year</slot:title>
                <slot:description>Set the dates and reporting periods.</slot:description>
                <slot:footer><x-help-tooltip label="Academic year setup help">After the calendar is saved, continue through teaching approach, classes, subjects, and publishing.</x-help-tooltip></slot:footer>
                <slot:content><april:button-link href="{{ route('academic-years.create', ['setup' => 1]) }}">Set up an academic year</april:button-link></slot:content>
            </april:card>
        @else
            <april:card>
                <slot:title>School setup is ready to continue</slot:title>
                <slot:description>The school essentials are in place.</slot:description>
                <slot:footer><x-help-tooltip label="Next steps help">You can now invite staff, add students, and configure optional school tools.</x-help-tooltip></slot:footer>
                <slot:content class="flex flex-wrap gap-3">
                    <april:button-link href="{{ route('dashboard') }}">Go to dashboard</april:button-link>
                    <april:button-link href="{{ route('admins.index') }}" variant="outline">Invite staff</april:button-link>
                </slot:content>
            </april:card>
        @endif

        <div class="flex justify-end">
            <april:button-link href="{{ route('dashboard') }}" variant="ghost">Save and finish later</april:button-link>
        </div>
    </div>
@endsection
