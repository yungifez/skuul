@foreach ($levels as $academicLevel)
    @php
        $children = $childrenByParent->get($academicLevel->id, collect());
        $sections = $sectionsByLevel->get($academicLevel->id, collect());
        $hasChildren = $children->isNotEmpty() || $sections->isNotEmpty();
    @endphp

    <div class="space-y-2">
        <div class="flex items-start justify-between gap-3 rounded-md border bg-background p-3">
            <div class="flex min-w-0 items-start gap-2">
                <x-lucide-folder-tree class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                <div class="min-w-0">
                    <p class="font-semibold">{{ $academicLevel->name }}</p>
                    <p class="text-sm text-muted-foreground">
                        @if ($children->isNotEmpty())
                            Umbrella group · {{ $children->count() }} {{ $children->count() === 1 ? 'level' : 'levels' }}
                        @else
                            {{ $sections->count() }} {{ $sections->count() === 1 ? strtolower(school_term('section', 'section')) : strtolower(school_terms('section', 'sections')) }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-1">
                @can('create', \App\Models\AcademicLevel::class)
                    <april:button-link href="{{ route('academic-levels.create', ['parent_id' => $academicLevel->id, 'setup' => 1, 'academic_year_id' => $academicYear->id]) }}" variant="outline" size="sm">Add class</april:button-link>
                @endcan
                @can('view', $academicLevel)
                    <april:button-link href="{{ route('academic-levels.show', $academicLevel) }}" variant="ghost" size="sm">View level</april:button-link>
                @endcan
            </div>
        </div>

        @if ($hasChildren)
            <div class="ml-4 space-y-2 border-l pl-4">
                @foreach ($children as $child)
                    @include('pages.academic-year.partials.level-tree', [
                        'levels' => collect([$child]),
                        'childrenByParent' => $childrenByParent,
                        'sectionsByLevel' => $sectionsByLevel,
                        'academicYear' => $academicYear,
                    ])
                @endforeach

                @foreach ($sections as $section)
                    <div class="flex items-center justify-between gap-3 rounded-md bg-muted/30 px-3 py-2 text-sm">
                        <div class="flex min-w-0 items-center gap-2">
                            <span class="size-1.5 shrink-0 rounded-full bg-muted-foreground"></span>
                            @can('view', $section)
                                <a href="{{ route('academic-cycle-sections.show', $section) }}" class="font-medium hover:underline">{{ $section->name }}</a>
                            @else
                                <span class="font-medium">{{ $section->name }}</span>
                            @endcan
                            @if ($section->stream || $section->shift)
                                <span class="truncate text-muted-foreground">{{ collect([$section->stream, $section->shift])->filter()->join(' · ') }}</span>
                            @endif
                        </div>
                        <x-academic-structure-status :status="$section->status" />
                    </div>
                @endforeach
            </div>
        @else
            <div class="ml-4 flex items-center justify-between gap-3 border-l pl-4 text-sm">
                <span class="text-muted-foreground">No section added for this year</span>
                @can('create', \App\Models\AcademicCycleSection::class)
                    <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_year_id' => $academicYear->id, 'academic_level_id' => $academicLevel->id, 'setup' => 1]) }}" variant="outline" size="sm">Add section</april:button-link>
                @endcan
            </div>
        @endif
    </div>
@endforeach
