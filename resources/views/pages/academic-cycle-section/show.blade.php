@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => school_terms('section', 'Sections')],
    ['text' => $academicCycleSection->name, 'active'],
]])

@php
    $fullName = $academicCycleSection->academicLevel->name.' · '.$academicCycleSection->name.' · '.$academicCycleSection->academicYear->name;
    $sectionsTerm = strtolower(school_terms('section', 'sections'));
    $canCreateSection = auth()->user()->can('create', \App\Models\AcademicCycleSection::class);
    $canEdit = auth()->user()->can('update', $academicCycleSection) && $academicCycleSection->isEditable();
    $facts = [
        school_term('academic_year', 'School year') => $academicCycleSection->academicYear->name,
        'Label' => $academicCycleSection->label,
        'Stream' => $academicCycleSection->stream,
        'Shift' => $academicCycleSection->shift,
        'Language' => $academicCycleSection->language,
        'Room' => $academicCycleSection->room,
        'Capacity' => $academicCycleSection->capacity,
        school_term('homeroom_teacher', 'Class teacher') => $academicCycleSection->homeroomTeacher?->name,
    ];
@endphp

@section('title', $fullName)
@section('page_heading', $fullName)

@section('page_actions')
    <div class="flex items-center gap-2">
        @if ($canEdit)
            <april:button-link href="{{ route('academic-cycle-sections.edit', $academicCycleSection) }}" variant="outline" class="h-11 select-none">Edit</april:button-link>
        @endif
        @if ($canCreateSection)
            <april:dropdown-menu>
                <slot:trigger>
                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More actions">
                        <x-lucide-ellipsis class="size-4" />
                    </april:button>
                </slot:trigger>
                <slot:content align="end" class="w-60">
                    <april:dropdown-menu-item href="{{ route('academic-cycle-sections.create', ['academic_year_id' => $academicCycleSection->academic_year_id, 'academic_level_id' => $academicCycleSection->academic_level_id]) }}"><x-lucide-plus class="mr-2 size-4" />Add another</april:dropdown-menu-item>
                    <april:dropdown-menu-item href="{{ route('academic-cycle-sections.roll-forward.show', ['source_academic_year_id' => $academicCycleSection->academic_year_id]) }}"><x-lucide-copy class="mr-2 size-4" />Roll into another year</april:dropdown-menu-item>
                </slot:content>
            </april:dropdown-menu>
        @endif
    </div>
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-10">
        <livewire:academic-structure-status-control :record="$academicCycleSection" />

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4">
            <div>
                <dt class="text-sm text-muted-foreground">{{ school_term('class_level', 'Class') }}</dt>
                <dd class="font-medium">
                    <a href="{{ route('academic-levels.show', $academicCycleSection->academicLevel) }}" class="underline-offset-4 hover:underline">{{ $academicCycleSection->academicLevel->name }}</a>
                </dd>
            </div>
            @foreach ($facts as $term => $value)
                <div>
                    <dt class="text-sm text-muted-foreground">{{ $term }}</dt>
                    <dd class="truncate font-medium">{{ $value ?? '—' }}</dd>
                </div>
            @endforeach
        </dl>

        <section aria-labelledby="siblings-heading" class="flex flex-col gap-2">
            <h2 id="siblings-heading" class="text-base font-semibold">Other {{ $sectionsTerm }}</h2>
            @if ($siblings->isEmpty())
                <p class="text-sm text-muted-foreground">No other {{ $sectionsTerm }}</p>
            @else
                <ul class="divide-y border-y">
                    @foreach ($siblings as $sibling)
                        <li>
                            <a href="{{ route('academic-cycle-sections.show', $sibling) }}" class="flex min-h-11 items-center justify-between gap-4 py-2 text-sm hover:bg-muted/50">
                                <span class="min-w-0 truncate font-medium">{{ $sibling->label ?? $sibling->name }}</span>
                                <span @class(['shrink-0', 'text-muted-foreground' => $sibling->status !== \App\Enums\AcademicStructureStatus::Active])>{{ $sibling->status->label() }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
