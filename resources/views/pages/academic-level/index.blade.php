@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-levels.index'), 'text' => school_terms('class_level', 'Class'), 'active'],
]])

@section('title', school_terms('class_level', 'Class'))
@section('page_heading', school_terms('class_level', 'Class'))

@section('page_actions')
    <x-resource-create-action :href="route('academic-levels.create')" ability="create" :arguments="[\App\Models\AcademicLevel::class]">Add {{ school_term('class_level', 'class') }}</x-resource-create-action>
@endsection

@section('content')
    @php
        use App\Enums\AcademicStructureStatus;

        $statusFilters = [['value' => null, 'text' => 'All']];
        foreach (AcademicStructureStatus::cases() as $case) {
            $statusFilters[] = ['value' => $case->value, 'text' => $case->label()];
        }
    @endphp

    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Filter by status">
                @foreach ($statusFilters as $filter)
                    <april:button-link
                        href="{{ route('academic-levels.index', array_filter(['status' => $filter['value']])) }}"
                        variant="{{ $status?->value === $filter['value'] ? 'default' : 'outline' }}"
                        :aria-current="$status?->value === $filter['value'] ? 'page' : null"
                        class="h-11 select-none">{{ $filter['text'] }}</april:button-link>
                @endforeach
            </nav>
            <div class="flex items-center gap-1">
                <april:button-link href="{{ route('academic-cycle-sections.index') }}" variant="ghost" class="h-11 select-none">{{ school_terms('section', 'Section') }}</april:button-link>
                <x-help-tooltip label="Classes and sections help">A class is the learner’s level, such as Primary 4, Grade 4, or Form 2. A section is one named group inside that class for one exact school year, such as Primary 4 · Green · 2026–2027.</x-help-tooltip>
            </div>
        </div>

        @if ($totalCount === 0)
            <x-empty-state
                icon="lucide-graduation-cap"
                title="No {{ strtolower(school_terms('class_level', 'Class')) }} yet"
                description="A {{ strtolower(school_term('section', 'section')) }} needs a {{ strtolower(school_term('class_level', 'class')) }} first, such as Primary 1.">
                <x-resource-create-action :href="route('academic-levels.create')" ability="create" :arguments="[\App\Models\AcademicLevel::class]">Add {{ school_term('class_level', 'class') }}</x-resource-create-action>
            </x-empty-state>
        @elseif (!$hasMatches)
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <p class="text-muted-foreground">No {{ strtolower(school_term('class_level', 'class')) }} is {{ strtolower($status->label()) }}. This school has <span class="tabular-nums">{{ $totalCount }}</span> in all.</p>
                <april:button-link href="{{ route('academic-levels.index') }}" variant="outline" class="h-11 select-none">Show every {{ strtolower(school_term('class_level', 'class')) }}</april:button-link>
            </div>
        @else
            @livewire('academic-year-structure-tree', [
                'allowWithoutAcademicYear' => true,
                'setupLinks' => false,
                'status' => $status?->value,
            ])
        @endif
    </div>
@endsection
