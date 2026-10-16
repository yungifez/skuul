    @php
        $stepItems = collect($progress['steps'])->map(function (array $step) use ($school, $currentStep): array {
            $state = $step['value'] === $currentStep->value ? 'current' : ($step['complete'] ? 'complete' : 'upcoming');

        return Illuminate\Support\Arr::except($step, ['description']) + [
            'state' => $state,
            'href' => $state === 'complete' ? route('schools.setup', [$school, $step['value']]) : null,
        ];
        })->all();
        $classesStepComplete = (bool) data_get(
            collect($progress['steps'])->firstWhere('value', \App\Enums\SchoolSetupStep::Classes->value),
            'complete',
        );
    @endphp

@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup'],
    ['href' => route('schools.setup', [$school, $currentStep->value]), 'text' => 'Quick setup', 'active'],
]])

@section('title', 'Set up '.$school->name)
@section('page_heading', 'Set up '.$school->name)

@section('content')
    @php
        use App\Enums\SchoolSetupStep;

        $classesTitle = match (true) {
            !$classesStepComplete => 'Add the classes your school teaches',
            $academicYear === null => 'Create the first school year',
            default => 'Continue setting up '.$academicYear->name,
        };
    @endphp
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-8">
        <april:steps :items="$stepItems" :current="$currentStep->value" />

        <section aria-labelledby="setup-step-heading" class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 border-b pb-6 sm:flex-row sm:items-center sm:justify-between">
                @if ($currentStep === SchoolSetupStep::Details)
                    <h2 id="setup-step-heading" class="text-lg font-semibold">Confirm school details</h2>
                    <april:button-link href="{{ route('schools.edit', [$school, 'setup' => 1]) }}" class="h-11 shrink-0">Review school details</april:button-link>
                @elseif ($currentStep === SchoolSetupStep::Language)
                    <h2 id="setup-step-heading" class="text-lg font-semibold">Choose the school’s language</h2>
                    <april:button-link href="{{ route('schools.operating-profile.edit', ['setup' => 1]) }}" class="h-11 shrink-0">Set school language</april:button-link>
                @elseif ($currentStep === SchoolSetupStep::Classes)
                    <h2 id="setup-step-heading" class="text-lg font-semibold">{{ $classesTitle }}</h2>
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($academicYear && $previousAcademicYear)
                            <april:button-link href="{{ route('academic-cycle-sections.roll-forward.show', ['source_academic_year_id' => $previousAcademicYear->id, 'target_academic_year_id' => $academicYear->id, 'setup' => 1]) }}" variant="ghost" class="h-11">Roll over last year</april:button-link>
                        @endif
                        @if (!$classesStepComplete)
                            <april:button-link href="{{ route('academic-levels.create', ['setup' => 1, 'school_setup' => 1]) }}" class="h-11">Add a class or grade</april:button-link>
                        @elseif ($academicYear === null)
                            <april:button-link href="{{ route('academic-levels.create', ['setup' => 1, 'school_setup' => 1]) }}" variant="ghost" class="h-11">Add a class</april:button-link>
                            <april:button-link href="{{ route('academic-years.create', ['setup' => 1]) }}" class="h-11">Create the school year</april:button-link>
                        @else
                            <april:button-link href="{{ route('academic-levels.create', ['setup' => 1, 'school_setup' => 1]) }}" variant="ghost" class="h-11">Add a class</april:button-link>
                            <april:button-link href="{{ route('academic-years.setup', $academicYear) }}" class="h-11">Continue year setup</april:button-link>
                        @endif
                    </div>
                @elseif ($currentStep === SchoolSetupStep::AcademicYear)
                    <h2 id="setup-step-heading" class="text-lg font-semibold">Create the first school year</h2>
                    <april:button-link href="{{ route('academic-years.create', ['setup' => 1]) }}" class="h-11 shrink-0">Set up a school year</april:button-link>
                @else
                    <h2 id="setup-step-heading" class="text-lg font-semibold">The school essentials are in place</h2>
                    <div class="flex flex-wrap gap-2">
                        <april:button-link href="{{ route('admins.index') }}" variant="ghost" class="h-11">Invite staff</april:button-link>
                        <april:button-link href="{{ route('dashboard') }}" class="h-11">Go to dashboard</april:button-link>
                    </div>
                @endif
            </div>

            @if ($currentStep === SchoolSetupStep::Classes)
                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-base font-semibold">{{ $academicYear ? 'Classes and '.strtolower(school_terms('section', 'sections')) : 'Classes and grades' }}</h3>
                        <a href="{{ route('academic-levels.index') }}" class="inline-flex min-h-11 items-center text-sm text-muted-foreground underline-offset-4 hover:text-foreground hover:underline">All classes</a>
                    </div>
                    @livewire('academic-year-structure-tree', ['academicYear' => $academicYear, 'schoolSetup' => true])
                </div>
            @endif
        </section>

        @if ($currentStep !== SchoolSetupStep::Finish)
            <div class="flex justify-end">
                <april:button-link href="{{ route('dashboard') }}" variant="ghost" class="h-11">Save and finish later</april:button-link>
            </div>
        @endif
    </div>
@endsection
