@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-levels.index'), 'text' => school_terms('class_level', 'Classes')],
    ['href' => route('academic-levels.show', $academicLevel), 'text' => $academicLevel->name, 'active'],
]])

@section('title', __($academicLevel->name))
@section('page_heading', __($academicLevel->name))

@section('page_actions')
    @can('update', $academicLevel)
        @if ($academicLevel->isEditable())
            <april:button-link href="{{ route('academic-levels.edit', $academicLevel) }}" variant="outline">
                <x-lucide-pencil class="mr-1.5 size-4" />
                Edit {{ strtolower(school_term('class_level', 'class')) }}
            </april:button-link>
        @endif
    @endcan
@endsection

@section('content')
    @php
        $sectionsByCycle = $cycleSections->groupBy(fn ($section) => $section->academicYear->name);
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <april:card class="lg:col-span-2">
            <slot:title>What this {{ strtolower(school_term('class_level', 'class')) }} is</slot:title>
            <slot:description>A reusable level a learner can be placed into. Use a parent level to organize groups such as Kindergarten → KG 1.</slot:description>
            <slot:content class="space-y-4">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-muted-foreground">{{ school_term('class_level', 'Class') }} name</dt>
                        <dd class="font-medium">{{ $academicLevel->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Short code</dt>
                        <dd class="font-medium">{{ $academicLevel->code ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Display order</dt>
                        <dd class="font-medium">{{ $academicLevel->position }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Level group</dt>
                        <dd class="font-medium">
                            @if ($academicLevel->parent)
                                <a href="{{ route('academic-levels.show', $academicLevel->parent) }}" class="hover:underline">{{ $academicLevel->parent->name }}</a>
                            @else
                                No parent group. This is a top-level group or standalone {{ strtolower(school_term('class_level', 'level')) }}.
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">{{ school_terms('class_level', 'Classes') }} under this one</dt>
                        <dd class="font-medium">{{ $academicLevel->children->isEmpty() ? 'None' : $academicLevel->children->pluck('name')->join(', ') }}</dd>
                    </div>
                </dl>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Status</slot:title>
                            <slot:description>An archived {{ strtolower(school_term('class_level', 'class')) }} can no longer receive a new {{ strtolower(school_term('section', 'section')) }}.</slot:description>
            <slot:content class="space-y-4">
                <x-academic-structure-status-control
                    :status="$academicLevel->status"
                    :action="route('academic-levels.status.update', $academicLevel)"
                    :can-update="auth()->user()->can('update', $academicLevel)"
                        archive-note="Archiving stops new {{ strtolower(school_terms('section', 'sections')) }}. Past sections, placements, and results keep naming this {{ strtolower(school_term('class_level', 'class')) }}." />

                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-muted-foreground">{{ school_terms('section', 'Sections') }}</dt>
                        <dd class="font-medium">{{ $cycleSections->count() }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-muted-foreground">{{ school_terms('academic_year', 'School years') }} covered</dt>
                        <dd class="font-medium">{{ $sectionsByCycle->count() }}</dd>
                    </div>
                </dl>

                @can('create', \App\Models\AcademicCycleSection::class)
                    @if ($academicLevel->status === \App\Enums\AcademicStructureStatus::Active)
                        <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id]) }}" class="w-full">
                            <x-lucide-plus class="mr-1.5 size-4" />
                            Add {{ strtolower(school_term('section', 'section')) }}
                        </april:button-link>
                    @endif
                @endcan
            </slot:content>
        </april:card>
    </div>

    <april:card class="mt-6">
        <slot:title>{{ school_terms('section', 'Sections') }} created for this {{ strtolower(school_term('class_level', 'class')) }}</slot:title>
        <slot:description>Each {{ strtolower(school_term('academic_year', 'school year')) }} keeps its own {{ strtolower(school_terms('section', 'sections')) }}. A later year needs its own, either created again or rolled forward.</slot:description>
        <slot:content>
            @if ($cycleSections->isEmpty())
                <x-empty-state
                    icon="lucide-layers"
                    title="No {{ strtolower(school_term('section', 'section')) }} uses this {{ strtolower(school_term('class_level', 'class')) }} yet"
                    description="Create the first named group, such as “Green”, for the {{ strtolower(school_term('academic_year', 'school year')) }} that will run it.">
                    @can('create', \App\Models\AcademicCycleSection::class)
                        <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id]) }}">
                            <x-lucide-plus class="mr-1.5 size-4" />
                            Add {{ strtolower(school_term('section', 'section')) }}
                        </april:button-link>
                    @endcan
                </x-empty-state>
            @else
                <div class="space-y-6">
                    @foreach ($sectionsByCycle as $cycleName => $sections)
                        <div>
                            <h3 class="mb-2 text-sm font-semibold">{{ $cycleName }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($sections as $section)
                                    <a href="{{ route('academic-cycle-sections.show', $section) }}" class="flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-accent">
                                        <span class="font-medium">{{ $section->label ?? $section->name }}</span>
                                        <x-academic-structure-status :status="$section->status" />
                                        <span class="text-muted-foreground">{{ $section->homeroomTeacher?->name ?? 'No '.strtolower(school_term('homeroom_teacher', 'class teacher')) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </slot:content>
    </april:card>
@endsection
