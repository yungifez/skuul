@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => school_terms('section', 'Sections')],
    ['href' => route('academic-cycle-sections.show', $academicCycleSection), 'text' => $academicCycleSection->name, 'active'],
]])

@php
    $fullName = $academicCycleSection->academicLevel->name.' · '.$academicCycleSection->name.' · '.$academicCycleSection->academicYear->name;
@endphp

@section('title', __($fullName))
@section('page_heading', __($fullName))

@section('page_actions')
    @can('update', $academicCycleSection)
        @if ($academicCycleSection->isEditable())
            <april:button-link href="{{ route('academic-cycle-sections.edit', $academicCycleSection) }}" variant="outline">
                <x-lucide-pencil class="mr-1.5 size-4" />
                Edit {{ strtolower(school_term('section', 'section')) }}
            </april:button-link>
        @endif
    @endcan
@endsection

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <april:card class="lg:col-span-2">
            <slot:title>Setup</slot:title>
            <slot:description>Everything here describes the group. Learner placement, teaching, and timetables are set elsewhere.</slot:description>
            <slot:content class="space-y-4">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-muted-foreground">{{ school_term('academic_year', 'School year') }}</dt>
                        <dd class="font-medium">{{ $academicCycleSection->academicYear->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">{{ school_term('class_level', 'Class') }}</dt>
                        <dd class="font-medium">
                            <a href="{{ route('academic-levels.show', $academicCycleSection->academicLevel) }}" class="hover:underline">{{ $academicCycleSection->academicLevel->name }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">{{ school_term('section', 'Section') }} name</dt>
                        <dd class="font-medium">{{ $academicCycleSection->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Local label</dt>
                        <dd class="font-medium">{{ $academicCycleSection->label ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Stream</dt>
                        <dd class="font-medium">{{ $academicCycleSection->stream ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Shift</dt>
                        <dd class="font-medium">{{ $academicCycleSection->shift ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Language of instruction</dt>
                        <dd class="font-medium">{{ $academicCycleSection->language ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Room</dt>
                        <dd class="font-medium">{{ $academicCycleSection->room ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Capacity</dt>
                        <dd class="font-medium">{{ $academicCycleSection->capacity ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Homeroom teacher</dt>
                        <dd class="font-medium">{{ $academicCycleSection->homeroomTeacher?->name ?? 'Not chosen' }}</dd>
                    </div>
                </dl>

            </slot:content>
        </april:card>

        <div class="space-y-6">
            <april:card>
                <slot:title>Status</slot:title>
                <slot:description>A draft is set up but not in use. Activate it when the setup is right.</slot:description>
                <slot:content class="space-y-4">
                    <x-academic-structure-status-control
                        :status="$academicCycleSection->status"
                        :action="route('academic-cycle-sections.status.update', $academicCycleSection)"
                        :can-update="auth()->user()->can('update', $academicCycleSection)"
                        archive-note="Archiving takes the section out of new work for this cycle. Records already made against it stay readable." />

                    @if (!$academicCycleSection->isEditable())
                        <p class="text-sm text-muted-foreground">The setup can no longer change. The {{ strtolower(school_term('section', 'section')) }} is archived, or its {{ strtolower(school_term('academic_year', 'school year')) }} is closed.</p>
                    @endif
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>One cycle only</slot:title>
                <slot:description>This section serves {{ $academicCycleSection->academicYear->name }}.</slot:description>
                <slot:content class="space-y-3">
                    <p class="text-sm text-muted-foreground">
                        For the next {{ strtolower(school_term('academic_year', 'school year')) }}, create the {{ strtolower(school_term('section', 'section')) }} again or roll the whole structure forward. The copy carries the setup only:
                        no learners, no teachers, no attendance, no results, or timetable entries.
                    </p>
                    @can('create', \App\Models\AcademicCycleSection::class)
                        <april:button-link href="{{ route('academic-cycle-sections.roll-forward.show', ['source_academic_year_id' => $academicCycleSection->academic_year_id]) }}" variant="outline" size="sm">
                            <x-lucide-copy class="mr-1.5 size-3.5" />
                            Roll {{ strtolower(school_terms('section', 'sections')) }} into another year
                        </april:button-link>
                    @endcan
                </slot:content>
            </april:card>
        </div>
    </div>

    <april:card class="mt-6">
        <slot:title>Other {{ strtolower(school_terms('section', 'sections')) }} of {{ $academicCycleSection->academicLevel->name }} in {{ $academicCycleSection->academicYear->name }}</slot:title>
        <slot:description>The parallel groups a learner in this {{ strtolower(school_term('class_level', 'class')) }} could be placed in.</slot:description>
        <slot:content>
            @if ($siblings->isEmpty())
                <x-empty-state
                    icon="lucide-layers"
                    title="This is the only {{ strtolower(school_term('section', 'section')) }} for the {{ strtolower(school_term('class_level', 'class')) }} in this year"
                    description="Add another when the {{ strtolower(school_term('class_level', 'class')) }} runs more than one group.">
                    @can('create', \App\Models\AcademicCycleSection::class)
                        <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_year_id' => $academicCycleSection->academic_year_id, 'academic_level_id' => $academicCycleSection->academic_level_id]) }}" variant="outline" size="sm">
                            <x-lucide-plus class="mr-1.5 size-3.5" />
                            Add another section
                        </april:button-link>
                    @endcan
                </x-empty-state>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach ($siblings as $sibling)
                        <a href="{{ route('academic-cycle-sections.show', $sibling) }}" class="flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-accent">
                            <span class="font-medium">{{ $sibling->label ?? $sibling->name }}</span>
                            <x-academic-structure-status :status="$sibling->status" />
                        </a>
                    @endforeach
                </div>
            @endif
        </slot:content>
    </april:card>
@endsection
