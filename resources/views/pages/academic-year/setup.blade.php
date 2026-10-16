@php
    $currentStepComplete = (bool) data_get(
        collect($progress['steps'])->firstWhere('value', $currentStep->value),
        'complete',
        false,
    );
    $nextStep = $currentStep->next();
    $stepItems = collect($progress['steps'])->map(function (array $step) use ($academicYear, $currentStep): array {
        $state = $step['value'] === $currentStep->value ? 'current' : ($step['complete'] ? 'complete' : 'upcoming');

        return \Illuminate\Support\Arr::except($step, 'description') + [
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
            @php
                $sections = $academicYear->cycleSections;
                $sectionsWithoutTeacher = $sections->whereNull('homeroom_teacher_id')->count();
                $canAddSection = $academicLevels->isNotEmpty();
                $sectionAddVariant = $currentStepComplete ? 'outline' : 'default';
            @endphp
            <section class="min-w-0 space-y-4" aria-labelledby="structure-heading">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div class="min-w-0">
                        <h2 id="structure-heading" class="text-lg font-semibold">{{ school_terms('class_level', 'Classes') }} and {{ strtolower(school_terms('section', 'sections')) }}</h2>
                        <p class="text-sm text-muted-foreground">
                            {{ $sections->count() }} {{ $sections->count() === 1 ? strtolower(school_term('section', 'section')) : strtolower(school_terms('section', 'sections')) }}
                            @if ($sectionsWithoutTeacher > 0)
                                · <span class="text-foreground">{{ $sectionsWithoutTeacher }} without a teacher</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <april:dropdown-menu>
                            <slot:trigger>
                                <april:button type="button" variant="ghost" size="icon" class="size-10 text-muted-foreground" aria-label="More structure actions">
                                    <x-lucide-ellipsis class="size-4" />
                                </april:button>
                            </slot:trigger>
                            <slot:content class="w-56">
                                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('academic-levels.index') }}'">
                                    <x-lucide-list-tree class="mr-2 size-4" />All {{ strtolower(school_terms('class_level', 'classes')) }}
                                </april:dropdown-menu-item>
                                <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('academic-cycle-sections.index', ['academic_year_id' => $academicYear->id]) }}'">
                                    <x-lucide-layers class="mr-2 size-4" />All {{ strtolower(school_terms('section', 'sections')) }} this year
                                </april:dropdown-menu-item>
                            </slot:content>
                        </april:dropdown-menu>
                        <april:button-link href="{{ route('academic-levels.create', ['setup' => 1, 'academic_year_id' => $academicYear->id]) }}" variant="{{ $canAddSection ? 'outline' : 'default' }}">
                            <x-lucide-folder-plus class="mr-1.5 size-4" />Add {{ strtolower(school_term('class_level', 'class')) }}
                        </april:button-link>
                        @if ($canAddSection)
                            <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_year_id' => $academicYear->id, 'setup' => 1]) }}" variant="{{ $sectionAddVariant }}">
                                <x-lucide-plus class="mr-1.5 size-4" />Add {{ strtolower(school_term('section', 'section')) }}
                            </april:button-link>
                        @endif
                    </div>
                </div>

                @if ($academicLevels->isEmpty())
                    <p class="border-y py-6 text-sm text-muted-foreground">No {{ strtolower(school_terms('class_level', 'classes')) }} yet.</p>
                @else
                    @livewire('academic-year-structure-tree', ['academicYear' => $academicYear])
                @endif
            </section>
        @else
            <section class="min-w-0 space-y-6" aria-labelledby="review-heading">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div class="min-w-0">
                        <h2 id="review-heading" class="text-lg font-semibold">Review {{ $academicYear->name }}</h2>
                        <dl class="mt-2 flex flex-wrap gap-x-8 gap-y-2 text-sm">
                            <div><dt class="text-muted-foreground">Dates</dt><dd class="font-medium">{{ $academicYear->starts_on?->format('M j, Y') }} – {{ $academicYear->ends_on?->format('M j, Y') }}</dd></div>
                            <div><dt class="text-muted-foreground">Reporting periods</dt><dd class="font-medium tabular-nums">{{ $academicYear->topLevelPeriods->count() }}</dd></div>
                        </dl>
                    </div>
                    @livewire('publish-academic-year', ['academicYear' => $academicYear])
                </div>

                @livewire('academic-year-structure-tree', ['academicYear' => $academicYear, 'setupLinks' => false, 'showLevelActions' => false])
            </section>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-2 border-t pt-4">
            @if (($previous = $currentStep->previous()) !== null)
                <april:button-link href="{{ route('academic-years.setup', [$academicYear, $previous->value]) }}" variant="ghost">
                    <x-lucide-arrow-left class="mr-1.5 size-4" />Back
                </april:button-link>
            @else
                <span></span>
            @endif
            <div class="flex flex-wrap items-center gap-2">
                <april:button-link href="{{ route('academic-years.show', $academicYear) }}" variant="ghost">Save and finish later</april:button-link>
                @if ($currentStepComplete && $nextStep !== null)
                    <april:button-link href="{{ route('academic-years.setup', [$academicYear, $nextStep->value]) }}">
                        Continue to {{ strtolower($nextStep->label()) }}
                        <x-lucide-arrow-right class="ml-1.5 size-4" />
                    </april:button-link>
                @endif
            </div>
        </div>
    </div>
@endsection
