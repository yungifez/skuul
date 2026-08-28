    @php
        $stepItems = collect($progress['steps'])->map(function (array $step) use ($school, $currentStep): array {
            $state = $step['value'] === $currentStep->value ? 'current' : ($step['complete'] ? 'complete' : 'upcoming');

        return $step + [
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
                <slot:title>Review classes and sections</slot:title>
                <slot:description>Review the reusable classes your school teaches, then create the sections that run this school year.</slot:description>
                <slot:footer><x-help-tooltip label="Class setup help">A class or grade is reusable, such as Kindergarten or Primary 4. A section is the group that runs in one school year, such as KG 1 Blue or Primary 4A. You can copy last year’s sections into this year and review them before they go live.</x-help-tooltip></slot:footer>
                <slot:content class="min-w-0 space-y-6">
                    <div class="flex flex-wrap gap-3">
                        <april:button-link href="{{ route('academic-levels.create', ['setup' => 1, 'school_setup' => 1]) }}">Add a class or grade</april:button-link>
                        <april:button-link href="{{ route('academic-levels.index') }}" variant="ghost">Manage reusable classes</april:button-link>
                        @if ($academicYear)
                            @if ($previousAcademicYear)
                                <april:button-link href="{{ route('academic-cycle-sections.roll-forward.show', ['source_academic_year_id' => $previousAcademicYear->id, 'target_academic_year_id' => $academicYear->id, 'setup' => 1]) }}" variant="outline">Roll over last year’s sections</april:button-link>
                            @endif
                            <april:button-link href="{{ route('academic-years.setup', $academicYear) }}" variant="ghost">Continue year setup</april:button-link>
                        @else
                            <april:button-link href="{{ route('academic-years.create', ['setup' => 1]) }}" variant="outline">Create the first school year</april:button-link>
                        @endif
                    </div>

                    @if (!$classesStepComplete)
                        <div class="flex flex-col gap-3 rounded-md border border-primary/30 bg-primary/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">First step</p>
                                <h3 class="font-semibold">Add your school’s reusable classes</h3>
                                <p class="text-sm text-muted-foreground">Start with the class levels your school teaches. You can add sections after you create a school year.</p>
                            </div>
                            <april:button-link href="{{ route('academic-levels.create', ['setup' => 1, 'school_setup' => 1]) }}" class="shrink-0">Add your first class</april:button-link>
                        </div>
                    @elseif (!$academicYear)
                        <div class="flex flex-col gap-3 rounded-md border border-primary/30 bg-primary/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Next step</p>
                                <h3 class="font-semibold">Create the first school year</h3>
                                <p class="text-sm text-muted-foreground">Your reusable classes are ready. Set the dates and reporting periods, then add this year’s sections.</p>
                            </div>
                            <april:button-link href="{{ route('academic-years.create', ['setup' => 1]) }}" class="shrink-0">Continue to school year</april:button-link>
                        </div>
                    @else
                        <div class="flex flex-col gap-3 rounded-md border border-primary/30 bg-primary/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Next step</p>
                                <h3 class="font-semibold">Continue setting up {{ $academicYear->name }}</h3>
                                <p class="text-sm text-muted-foreground">Review the remaining year setup steps, including sections, subjects, and publishing.</p>
                            </div>
                            <april:button-link href="{{ route('academic-years.setup', $academicYear) }}" class="shrink-0">Continue year setup</april:button-link>
                        </div>
                    @endif

                    <div class="space-y-3 border-t pt-5">
                        <div>
                            <h3 class="font-semibold">{{ $academicYear?->name ? $academicYear->name.' classes and sections' : 'Reusable classes and grades' }}</h3>
                            <p class="text-sm text-muted-foreground">{{ $academicYear ? 'Expand a class to review its sections, add another section, or change the display order.' : 'These are the levels your school can use in any school year. Create a school year before adding sections.' }}</p>
                        </div>
                        @livewire('academic-year-structure-tree', ['academicYear' => $academicYear, 'schoolSetup' => true])
                    </div>
                </slot:content>
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
