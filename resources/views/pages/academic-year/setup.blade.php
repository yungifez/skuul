@php
    $currentStepComplete = (bool) data_get(
        collect($progress['steps'])->firstWhere('value', $currentStep->value),
        'complete',
        false,
    );
    $nextStep = $currentStep->next();
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
    <div class="mx-auto flex w-full {{ $currentStep === \App\Enums\AcademicYearSetupStep::Structure ? 'max-w-7xl' : 'max-w-5xl' }} flex-col gap-6">
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
                <slot:description>Create this year’s sections inside the levels your school uses.</slot:description>
                <slot:footer><x-help-tooltip label="Class setup help">Add reusable classes or grades first. Then create this year’s sections or forms and assign class teachers.</x-help-tooltip></slot:footer>
                <slot:content class="min-w-0 space-y-6">
                    <div class="flex flex-wrap gap-3">
                        <april:button-link href="{{ route('academic-levels.create', ['setup' => 1, 'academic_year_id' => $academicYear->id]) }}">Add a level or group</april:button-link>
                        <april:button-link href="{{ route('academic-levels.index') }}" variant="ghost">Manage levels and groups</april:button-link>
                        @if ($academicYear->cycleSections->isEmpty())
                            <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_year_id' => $academicYear->id, 'setup' => 1]) }}" variant="outline">Add this year’s first section</april:button-link>
                        @endif
                        <april:button-link href="{{ route('academic-cycle-sections.index', ['academic_year_id' => $academicYear->id]) }}" variant="ghost">Manage this year’s sections</april:button-link>
                    </div>

                    <div class="space-y-3 border-t pt-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold">Classes and sections already added</h3>
                                <p class="text-sm text-muted-foreground">Expand a level to see its sections. Use the arrows to change display order.</p>
                            </div>
                            <x-help-tooltip label="Classes and sections help">A level is reusable, such as Primary 4 or Kindergarten. A section is the named group that runs in this school year, such as Green or KG 1 Blue.</x-help-tooltip>
                        </div>

                        @if ($academicLevels->isEmpty())
                            <div class="rounded-md border border-dashed p-4 text-sm text-muted-foreground">
                                No levels are available yet. Add a level or group before adding this year’s sections.
                            </div>
                        @else
                            @livewire('academic-year-structure-tree', ['academicYear' => $academicYear])
                        @endif
                    </div>
                </slot:content>
            </april:card>
        @elseif ($currentStep === \App\Enums\AcademicYearSetupStep::Subjects)
            <april:card>
                <slot:title>Add subjects to this year</slot:title>
                <slot:description>Choose what learners study, then connect each subject to the classes and periods where it is taught.</slot:description>
                <slot:footer><x-help-tooltip label="Subject setup help">A subject is the reusable school subject, such as Mathematics, English, or Science. Adding it to this year connects it to a class and a reporting period. It is saved for review before it is activated.</x-help-tooltip></slot:footer>
                <slot:content class="space-y-5">
                    @if ($subjects->isEmpty())
                        <div class="flex flex-col gap-3 rounded-md border border-dashed p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-semibold">Create your first subject</h3>
                                <p class="text-sm text-muted-foreground">There are no subjects in this school’s catalog yet.</p>
                            </div>
                            <april:button-link href="{{ route('subjects.create', ['setup' => 1, 'academic_year_id' => $academicYear->id]) }}" class="shrink-0">Create a subject</april:button-link>
                        </div>
                    @else
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-semibold">Subjects in the school catalog</h3>
                                <p class="text-sm text-muted-foreground">{{ $subjects->count() }} {{ $subjects->count() === 1 ? 'subject is' : 'subjects are' }} available for {{ $academicYear->name }}.</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <april:button-link href="{{ route('course-offerings.create', ['academic_year_id' => $academicYear->id, 'setup' => 1]) }}">Add a subject to this year</april:button-link>
                                <april:button-link href="{{ route('course-offerings.bulk-create', ['academic_year_id' => $academicYear->id, 'setup' => 1]) }}" variant="outline">Set up across levels</april:button-link>
                                @if ($previousAcademicYear !== null)
                                    <april:button-link href="{{ route('course-offerings.roll-forward.show', ['source_academic_year_id' => $previousAcademicYear->id, 'target_academic_year_id' => $academicYear->id, 'setup' => 1]) }}" variant="outline">Roll over last year's subjects</april:button-link>
                                @endif
                                <april:button-link href="{{ route('subjects.create', ['setup' => 1, 'academic_year_id' => $academicYear->id]) }}" variant="outline">Create another subject</april:button-link>
                            </div>
                        </div>
                        <div class="space-y-3 border-t pt-5">
                            <div>
                                <h3 class="font-semibold">Subjects by class</h3>
                                <p class="text-sm text-muted-foreground">Expand a class to see its sections. Each subject lists the periods in which it is offered.</p>
                            </div>
                            @livewire('academic-year-structure-tree', ['academicYear' => $academicYear, 'setupLinks' => false, 'showLevelActions' => false])
                        </div>
                    @endif
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

            <april:card>
                <slot:title>Classes, sections, and subjects</slot:title>
                <slot:description>Review the structure for {{ $academicYear->name }}. Expand each level to see its sections and offerings.</slot:description>
                <slot:content>
                    @livewire('academic-year-structure-tree', ['academicYear' => $academicYear, 'setupLinks' => false, 'showLevelActions' => false])
                </slot:content>
            </april:card>
        @endif

        @if ($currentStepComplete && $nextStep !== null)
            <div class="flex flex-col gap-3 rounded-lg border border-primary/30 bg-primary/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold">Next step: {{ $nextStep->label() }}</p>
                    <p class="mt-1 text-sm text-muted-foreground">This step is complete. Continue setting up {{ strtolower($academicYear->name) }} when you are ready.</p>
                </div>
                <april:button-link href="{{ route('academic-years.setup', [$academicYear, $nextStep->value]) }}" class="shrink-0">
                    Continue to {{ strtolower($nextStep->label()) }}
                </april:button-link>
            </div>
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
