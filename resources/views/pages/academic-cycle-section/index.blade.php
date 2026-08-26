@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => school_terms('section', 'Section').' this year', 'active'],
]])

@section('title', school_terms('section', 'Section').' this year')
@section('page_heading', school_terms('section', 'Section').' this year')

@section('page_actions')
    <x-resource-create-action :href="route('academic-cycle-sections.create')" ability="create" :arguments="[\App\Models\AcademicCycleSection::class]">Add {{ school_term('section', 'section') }}</x-resource-create-action>
@endsection

@section('content')
    @php
        use App\Enums\AcademicStructureStatus;

        $selectedCycle = $academicYears->firstWhere('id', $selectedAcademicYearId);
        $isCurrentCycle = $selectedAcademicYearId !== null && $selectedAcademicYearId === current_academic_year_id();
        $filtered = $selectedAcademicYearId !== null || $selectedAcademicLevelId !== null || $selectedStatus !== null;
        $lastGroup = null;
    @endphp

    <april:card class="mb-6">
        <slot:title class="flex items-center gap-1">
            <span>{{ $selectedCycle?->name ?? 'Every '.strtolower(school_term('academic_year', 'school year')) }}</span>
            <x-help-tooltip label="Year groups help">A section is one named group inside a class for one exact school year. It is not reused; a later year gets its own section.</x-help-tooltip>
        </slot:title>
        <slot:description>Filter the groups running in each school year.</slot:description>
        <slot:content class="space-y-4">
            @if ($isCurrentCycle)
                <p class="text-sm text-muted-foreground">
                    <x-lucide-circle-dot class="mr-1 inline size-3.5" />
                    This is the cycle you are working in. Change the filter to look at another one.
                </p>
            @endif

            <form method="GET" action="{{ route('academic-cycle-sections.index') }}" class="grid gap-3 md:grid-cols-[1fr_1fr_1fr_auto] md:items-end">
                <div class="flex flex-col gap-1.5">
                    <april:label for="filter-cycle">{{ school_term('academic_year', 'School year') }}</april:label>
                    <select id="filter-cycle" name="academic_year_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">Every {{ strtolower(school_term('academic_year', 'school year')) }}</option>
                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" {{ $selectedAcademicYearId === $academicYear->id ? 'selected' : '' }}>{{ $academicYear->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <april:label for="filter-level">{{ school_term('class_level', 'Class') }}</april:label>
                    <select id="filter-level" name="academic_level_id" class="rounded-md border border-input bg-background px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">Every {{ strtolower(school_terms('class_level', 'class')) }}</option>
                        @foreach ($academicLevels as $academicLevel)
                            <option value="{{ $academicLevel->id }}" {{ $selectedAcademicLevelId === $academicLevel->id ? 'selected' : '' }}>{{ $academicLevel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <april:label for="filter-status">Status</april:label>
                    <select id="filter-status" name="status" class="rounded-md border border-input bg-background px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="">Every status</option>
                        @foreach (AcademicStructureStatus::cases() as $case)
                            <option value="{{ $case->value }}" {{ $selectedStatus === $case ? 'selected' : '' }}>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <april:button type="submit" variant="outline">Apply</april:button>
                    @if ($filtered)
                        <april:button-link href="{{ route('academic-cycle-sections.index', ['academic_year_id' => '']) }}" variant="ghost">Clear</april:button-link>
                    @endif
                </div>
            </form>

            @can('create', \App\Models\AcademicCycleSection::class)
                <div class="flex flex-wrap items-center gap-2 border-t pt-4">
                    <april:button-link href="{{ route('academic-cycle-sections.roll-forward.show') }}" variant="outline" size="sm">
                        <x-lucide-copy class="mr-1.5 size-3.5" />
                        Roll {{ strtolower(school_terms('section', 'sections')) }} into another year
                    </april:button-link>
                    <april:button-link href="{{ route('academic-levels.index') }}" variant="ghost" size="sm">Manage {{ strtolower(school_terms('class_level', 'classes')) }}</april:button-link>
                </div>
            @endcan
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>{{ school_terms('section', 'Section') }} this year</slot:title>
        <slot:description>Groups are shown by school year and class.</slot:description>
        <slot:content>
            @if ($totalCount === 0)
                    <x-empty-state
                    icon="lucide-layers"
                    title="No {{ strtolower(school_term('section', 'section')) }} exists yet"
                    description="Create the first named group for a cycle, such as Primary 4 · Green · 2026–2027. It starts as a draft, so nothing goes live until you activate it.">
                    <x-resource-create-action :href="route('academic-cycle-sections.create')" ability="create" :arguments="[\App\Models\AcademicCycleSection::class]">Add {{ strtolower(school_term('section', 'section')) }}</x-resource-create-action>
                </x-empty-state>
            @elseif ($academicCycleSections->isEmpty())
                <x-empty-state
                    icon="lucide-filter"
                    title="No section matches this filter"
                    description="{{ $selectedCycle ? $selectedCycle->name.' has no '.strtolower(school_term('section', 'section')).' that matches.' : 'Nothing matches the chosen filter.' }} This school has {{ $totalCount }} {{ strtolower(school_terms('section', 'sections')) }} in total.">
                    <april:button-link href="{{ route('academic-cycle-sections.index', ['academic_year_id' => '']) }}" variant="outline" size="sm">Show every {{ strtolower(school_term('academic_year', 'school year')) }}</april:button-link>
                    @can('create', \App\Models\AcademicCycleSection::class)
                        <april:button-link href="{{ route('academic-cycle-sections.roll-forward.show') }}" variant="outline" size="sm">Roll {{ strtolower(school_terms('section', 'sections')) }} forward</april:button-link>
                    @endcan
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[980px] text-sm">
                        <thead class="border-b text-left text-muted-foreground">
                            <tr>
                                <th class="px-3 py-2">{{ school_term('section', 'Section') }}</th>
                                <th class="px-3 py-2">Stream / shift</th>
                                <th class="px-3 py-2">Room</th>
                                <th class="px-3 py-2">Capacity</th>
                                <th class="px-3 py-2">{{ school_term('homeroom_teacher', 'Class teacher') }}</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($academicCycleSections as $academicCycleSection)
                                @php
                                    $group = $academicCycleSection->academicYear->name.' · '.$academicCycleSection->academicLevel->name;
                                    $isNewGroup = $group !== $lastGroup;
                                    $lastGroup = $group;
                                @endphp
                                @if ($isNewGroup)
                                    <tr class="bg-muted/40">
                                        <th colspan="7" class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                                            {{ $academicCycleSection->academicYear->name }}
                                            <span class="mx-1">›</span>
                                            <a href="{{ route('academic-levels.show', $academicCycleSection->academicLevel) }}" class="hover:underline">{{ $academicCycleSection->academicLevel->name }}</a>
                                        </th>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="px-3 py-3">
                                        <a href="{{ route('academic-cycle-sections.show', $academicCycleSection) }}" class="font-medium hover:underline">{{ $academicCycleSection->label ?? $academicCycleSection->name }}</a>
                                    </td>
                                    <td class="px-3 py-3">{{ collect([$academicCycleSection->stream, $academicCycleSection->shift])->filter()->join(' · ') ?: '—' }}</td>
                                    <td class="px-3 py-3">{{ $academicCycleSection->room ?? '—' }}</td>
                                    <td class="px-3 py-3">{{ $academicCycleSection->capacity ?? 'Not set' }}</td>
                                    <td class="px-3 py-3">{{ $academicCycleSection->homeroomTeacher?->name ?? 'Not chosen' }}</td>
                                    <td class="px-3 py-3"><x-academic-structure-status :status="$academicCycleSection->status" /></td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <april:button-link href="{{ route('academic-cycle-sections.show', $academicCycleSection) }}" variant="ghost" size="sm">View</april:button-link>
                                            @can('update', $academicCycleSection)
                                                @if ($academicCycleSection->isEditable())
                                                    <april:button-link href="{{ route('academic-cycle-sections.edit', $academicCycleSection) }}" variant="outline" size="sm">Edit</april:button-link>
                                                @endif
                                                @if ($academicCycleSection->status === AcademicStructureStatus::Draft)
                                                    <form method="POST" action="{{ route('academic-cycle-sections.status.update', $academicCycleSection) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="{{ AcademicStructureStatus::Active->value }}">
                                                        <april:button size="sm" type="submit">Activate</april:button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $academicCycleSections->links() }}</div>
            @endif
        </slot:content>
    </april:card>
@endsection
