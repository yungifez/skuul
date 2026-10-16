<div class="flex flex-col gap-6">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $sectionWord = strtolower(school_term('section', 'section'));
        $sectionsWord = strtolower(school_terms('section', 'Section'));
        $canCreate = auth()->user()->can('create', \App\Models\AcademicCycleSection::class);
        $lastGroup = null;
    @endphp

    <div class="grid gap-3 sm:grid-cols-3">
        <div>
            <label for="filter-cycle" class="text-sm text-muted-foreground">{{ school_term('academic_year', 'School year') }}</label>
            <select id="filter-cycle" wire:model.live="academicYearId" class="{{ $controlClasses }}">
                <option value="">Every {{ strtolower(school_term('academic_year', 'school year')) }}</option>
                @foreach ($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="filter-level" class="text-sm text-muted-foreground">{{ school_term('class_level', 'Class') }}</label>
            <select id="filter-level" wire:model.live="academicLevelId" class="{{ $controlClasses }}">
                <option value="">Every {{ strtolower(school_term('class_level', 'class')) }}</option>
                @foreach ($academicLevels as $academicLevel)
                    <option value="{{ $academicLevel->id }}">{{ $academicLevel->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="filter-status" class="text-sm text-muted-foreground">Status</label>
            <select id="filter-status" wire:model.live="status" class="{{ $controlClasses }}">
                <option value="">Every status</option>
                @foreach (\App\Enums\AcademicStructureStatus::cases() as $case)
                    <option value="{{ $case->value }}">{{ $case->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($totalCount === 0)
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <p class="text-muted-foreground">No {{ $sectionWord }} yet. A new one starts as a draft.</p>
        </div>
    @elseif ($academicCycleSections->isEmpty())
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <p class="text-muted-foreground">{{ $selectedYear ? $selectedYear->name.' has no '.$sectionWord.' that matches.' : 'No '.$sectionWord.' matches.' }} This school has <span class="tabular-nums">{{ $totalCount }}</span> in all.</p>
            @if ($isFiltered)
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="clearFilters">Show every {{ $sectionWord }}</april:button>
            @endif
            @if ($canCreate && $selectedYear)
                <april:button-link href="{{ route('academic-cycle-sections.roll-forward.show', ['target_academic_year_id' => $selectedYear->id]) }}" variant="outline" class="h-11 select-none">Roll {{ $sectionsWord }} into {{ $selectedYear->name }}</april:button-link>
            @endif
        </div>
    @else
        <ul class="divide-y border-y" wire:loading.class="opacity-60" wire:target="academicYearId,academicLevelId,status,clearFilters,gotoPage,nextPage,previousPage">
            @foreach ($academicCycleSections as $academicCycleSection)
                @php
                    $group = $academicCycleSection->academicYear->name.' · '.$academicCycleSection->academicLevel->name;
                    $isNewGroup = $group !== $lastGroup;
                    $lastGroup = $group;
                    $details = collect([$academicCycleSection->stream, $academicCycleSection->shift, $academicCycleSection->room])->filter()->join(' · ');
                @endphp
                @if ($isNewGroup)
                    <li wire:key="group-{{ $academicCycleSection->academic_year_id }}-{{ $academicCycleSection->academic_level_id }}" class="bg-muted/40 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                        {{ $academicCycleSection->academicYear->name }} › {{ $academicCycleSection->academicLevel->name }}
                    </li>
                @endif
                <li wire:key="section-{{ $academicCycleSection->id }}" class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                    <div class="min-w-0 text-sm">
                        <a href="{{ route('academic-cycle-sections.show', $academicCycleSection) }}" class="font-medium underline-offset-4 hover:underline">{{ $academicCycleSection->label ?? $academicCycleSection->name }}</a>
                        <p class="text-muted-foreground">
                            {{ $details !== '' ? $details : '—' }}
                            · <span class="tabular-nums">{{ $academicCycleSection->capacity ?? '—' }}</span> places
                            · {{ $academicCycleSection->homeroomTeacher?->name ?? '—' }}
                        </p>
                    </div>
                    <livewire:academic-structure-status-control :record="$academicCycleSection" :key="'status-'.$academicCycleSection->id" />
                </li>
            @endforeach
        </ul>
        {{ $academicCycleSections->links('components.datatable-pagination-links-view') }}
    @endif

    @if ($canCreate)
        <div class="flex flex-wrap gap-2">
            <april:button-link href="{{ route('academic-cycle-sections.roll-forward.show') }}" variant="outline" class="h-11 select-none">Roll {{ $sectionsWord }} into another year</april:button-link>
            <april:button-link href="{{ route('academic-levels.index') }}" variant="ghost" class="h-11 select-none">Manage {{ strtolower(school_terms('class_level', 'Class')) }}</april:button-link>
        </div>
    @endif
</div>
