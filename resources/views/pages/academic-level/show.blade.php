@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-levels.index'), 'text' => school_terms('class_level', 'Classes')],
    ['text' => $academicLevel->name, 'active'],
]])

@section('title', $academicLevel->name)
@section('page_heading', $academicLevel->name)

@php
    $sectionTerm = strtolower(school_term('section', 'section'));
    $canAddSection = !$academicLevel->is_group
        && $academicLevel->status === \App\Enums\AcademicStructureStatus::Active
        && auth()->user()->can('create', \App\Models\AcademicCycleSection::class);
    $sectionsByCycle = $cycleSections->groupBy(fn ($section) => $section->academicYear->name);
@endphp

@section('page_actions')
    <div class="flex items-center gap-2">
        @if ($canAddSection)
            <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id]) }}" class="h-11 select-none">
                <x-lucide-plus class="mr-2 size-4" />Add {{ $sectionTerm }}
            </april:button-link>
        @endif
        @can('update', $academicLevel)
            @if ($academicLevel->isEditable())
                <april:button-link href="{{ route('academic-levels.edit', $academicLevel) }}" variant="outline" class="h-11 select-none">Edit</april:button-link>
            @endif
        @endcan
    </div>
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-4xl flex-col gap-10">
        <livewire:academic-structure-status-control :record="$academicLevel" />

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4">
            <div>
                <dt class="text-sm text-muted-foreground">Code</dt>
                <dd class="font-medium">{{ $academicLevel->code ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Order</dt>
                <dd class="font-medium tabular-nums">{{ $academicLevel->position }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">{{ $academicLevel->is_group ? 'Kind' : 'Group' }}</dt>
                <dd class="font-medium">
                    @if ($academicLevel->is_group)
                        Group
                    @elseif ($academicLevel->parent)
                        <a href="{{ route('academic-levels.show', $academicLevel->parent) }}" class="underline-offset-4 hover:underline">{{ $academicLevel->parent->name }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">{{ school_terms('section', 'Sections') }}</dt>
                <dd class="font-medium tabular-nums">{{ $cycleSections->count() }}</dd>
            </div>
            @if ($academicLevel->children->isNotEmpty())
                <div class="col-span-2 sm:col-span-4">
                    <dt class="text-sm text-muted-foreground">{{ school_terms('class_level', 'Classes') }} in this group</dt>
                    <dd class="flex flex-wrap gap-x-3 font-medium">
                        @foreach ($academicLevel->children as $child)
                            <a href="{{ route('academic-levels.show', $child) }}" class="underline-offset-4 hover:underline">{{ $child->name }}</a>
                        @endforeach
                    </dd>
                </div>
            @endif
        </dl>

        <section aria-labelledby="sections-heading" class="flex flex-col gap-6">
            <h2 id="sections-heading" class="text-base font-semibold">{{ school_terms('section', 'Sections') }}</h2>
            @if ($cycleSections->isEmpty())
                <p class="text-sm text-muted-foreground">No {{ strtolower(school_terms('section', 'sections')) }}</p>
            @else
                @foreach ($sectionsByCycle as $cycleName => $sections)
                    <div class="flex flex-col gap-2">
                        <h3 class="text-sm text-muted-foreground">{{ $cycleName }}</h3>
                        <ul class="divide-y border-y">
                            @foreach ($sections as $section)
                                <li>
                                    <a href="{{ route('academic-cycle-sections.show', $section) }}" class="flex min-h-11 items-center justify-between gap-4 py-2 text-sm hover:bg-muted/50">
                                        <span class="min-w-0 truncate font-medium">{{ $section->label ?? $section->name }}</span>
                                        <span class="flex shrink-0 items-center gap-3 text-muted-foreground">
                                            <span class="hidden truncate sm:inline">{{ $section->homeroomTeacher?->name ?? '—' }}</span>
                                            <span @class(['text-foreground' => $section->status === \App\Enums\AcademicStructureStatus::Active])>{{ $section->status->label() }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            @endif
        </section>
    </div>
@endsection
