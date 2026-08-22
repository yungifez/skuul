@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-levels.index'), 'text' => 'Academic levels', 'active'],
]])

@section('title', __('Academic levels'))
@section('page_heading', __('Academic levels'))

@section('page_actions')
    <x-resource-create-action :href="route('academic-levels.create')" ability="create" :arguments="[\App\Models\AcademicLevel::class]">Add academic level</x-resource-create-action>
@endsection

@section('content')
    @php
        use App\Enums\AcademicStructureStatus;

        $statusFilters = [['value' => null, 'text' => 'All']];
        foreach (AcademicStructureStatus::cases() as $case) {
            $statusFilters[] = ['value' => $case->value, 'text' => $case->label()];
        }
    @endphp

    <april:card class="mb-6">
        <slot:title>Levels are reusable. Sections are not.</slot:title>
        <slot:description>
            A level is the step a learner is at, such as Primary 4, Grade 4, or Form 2. It stays the same year after year.
            A cycle section is one named group inside a level for one exact academic cycle, such as Primary 4 · Green · 2026–2027.
        </slot:description>
        <slot:content class="flex flex-wrap gap-2">
            <april:button-link href="{{ route('academic-cycle-sections.index', ['academic_year_id' => '']) }}" variant="outline" size="sm">
                <x-lucide-layers class="mr-1.5 size-3.5" />
                Go to cycle sections
            </april:button-link>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>All academic levels</slot:title>
        <slot:description>Set the display order once. Every screen that lists levels reads it.</slot:description>
        <slot:content>
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="text-sm text-muted-foreground">Status</span>
                @foreach ($statusFilters as $filter)
                    <april:button-link
                        href="{{ route('academic-levels.index', array_filter(['status' => $filter['value']])) }}"
                        variant="{{ $status?->value === $filter['value'] ? 'default' : 'outline' }}"
                        size="sm">{{ $filter['text'] }}</april:button-link>
                @endforeach
            </div>

            @if ($totalCount === 0)
                <x-empty-state
                    icon="lucide-graduation-cap"
                    title="No academic levels yet"
                    description="Add the levels this school teaches, such as Primary 1 to Primary 6. You need at least one level before you can create a section for an academic cycle.">
                    <x-resource-create-action :href="route('academic-levels.create')" ability="create" :arguments="[\App\Models\AcademicLevel::class]">Add academic level</x-resource-create-action>
                </x-empty-state>
            @elseif ($academicLevels->isEmpty())
                <x-empty-state
                    icon="lucide-filter"
                    title="No level matches this status"
                    description="This school has {{ $totalCount }} academic levels. Clear the filter to see them.">
                    <april:button-link href="{{ route('academic-levels.index') }}" variant="outline" size="sm">Show all levels</april:button-link>
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead class="border-b text-left text-muted-foreground">
                            <tr>
                                <th class="px-3 py-2">Level</th>
                                <th class="px-3 py-2">Local label</th>
                                <th class="px-3 py-2">Sits under</th>
                                <th class="px-3 py-2">Cycle sections</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($academicLevels as $academicLevel)
                                <tr>
                                    <td class="px-3 py-3">
                                        <a href="{{ route('academic-levels.show', $academicLevel) }}" class="font-medium hover:underline">{{ $academicLevel->name }}</a>
                                        @if ($academicLevel->code)
                                            <span class="ml-1 text-xs text-muted-foreground">{{ $academicLevel->code }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">{{ $academicLevel->label ?? '—' }}</td>
                                    <td class="px-3 py-3">{{ $academicLevel->parent?->name ?? '—' }}</td>
                                    <td class="px-3 py-3">
                                        @if ($academicLevel->cycle_sections_count === 0)
                                            <span class="text-muted-foreground">None yet</span>
                                        @else
                                            <a href="{{ route('academic-cycle-sections.index', ['academic_level_id' => $academicLevel->id, 'academic_year_id' => '']) }}" class="hover:underline">
                                                {{ $academicLevel->cycle_sections_count }} total · {{ $academicLevel->active_cycle_sections_count }} active
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3"><x-academic-structure-status :status="$academicLevel->status" /></td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <april:button-link href="{{ route('academic-levels.show', $academicLevel) }}" variant="ghost" size="sm">View</april:button-link>
                                            @can('update', $academicLevel)
                                                @if ($academicLevel->isEditable())
                                                    <april:button-link href="{{ route('academic-levels.edit', $academicLevel) }}" variant="outline" size="sm">Edit</april:button-link>
                                                @endif
                                            @endcan
                                            @can('create', \App\Models\AcademicCycleSection::class)
                                                @if ($academicLevel->status === AcademicStructureStatus::Active)
                                                    <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id]) }}" variant="outline" size="sm">Add section</april:button-link>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $academicLevels->links() }}</div>
            @endif
        </slot:content>
    </april:card>
@endsection
