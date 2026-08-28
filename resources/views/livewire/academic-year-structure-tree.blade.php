<div wire:loading.class="opacity-70" wire:target="moveLevel,moveSection" class="w-full min-w-0 transition-opacity">
    @php
        $sectionsByLevel = $academicYear->cycleSections->groupBy('academic_level_id');
    @endphp

    @if ($academicLevels->isEmpty())
        <div class="rounded-md border border-dashed p-4 text-sm text-muted-foreground">
            No reusable classes or grades have been added yet. Add one above before creating this year’s sections.
        </div>
    @else
        @include('pages.academic-year.partials.level-tree', [
            'levels' => $academicLevels->whereNull('parent_id')->values(),
            'childrenByParent' => $academicLevels->groupBy('parent_id'),
            'sectionsByLevel' => $sectionsByLevel,
            'academicYear' => $academicYear,
            'schoolSetup' => $schoolSetup,
        ])
    @endif
</div>
