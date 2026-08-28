<div wire:loading.class="opacity-70" wire:target="moveLevel,moveSection" class="transition-opacity">
    @php
        $sectionsByLevel = $academicYear->cycleSections->groupBy('academic_level_id');
    @endphp

    @include('pages.academic-year.partials.level-tree', [
        'levels' => $academicLevels->whereNull('parent_id')->values(),
        'childrenByParent' => $academicLevels->groupBy('parent_id'),
        'sectionsByLevel' => $sectionsByLevel,
        'academicYear' => $academicYear,
    ])
</div>
