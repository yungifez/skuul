@foreach ($levels->values() as $levelIndex => $academicLevel)
    @php
        $children = $childrenByParent->get($academicLevel->id, collect());
        $sections = $sectionsByLevel->get($academicLevel->id, collect())->values();
        $hasChildren = $children->isNotEmpty() || $sections->isNotEmpty();
        $setupParameters = ['setup' => 1, 'academic_year_id' => $academicYear->id];

        if ($schoolSetup) {
            $setupParameters['school_setup'] = 1;
        }

        $levelSummary = $children->isNotEmpty()
            ? 'Umbrella group · '.$children->count().' '.($children->count() === 1 ? 'level' : 'levels')
            : $sections->count().' '.($sections->count() === 1 ? strtolower(school_term('section', 'section')) : strtolower(school_terms('section', 'sections')));

        if ($children->isNotEmpty() && $sections->isNotEmpty()) {
            $levelSummary .= ' · '.$sections->count().' '.($sections->count() === 1 ? strtolower(school_term('section', 'section')) : strtolower(school_terms('section', 'sections')));
        }
    @endphp

    <div wire:key="academic-level-{{ $academicLevel->id }}" class="w-full min-w-0 space-y-2">
        <details open class="group w-full min-w-0 rounded-md border bg-background p-3">
            <summary class="flex cursor-pointer list-none flex-col gap-3 marker:hidden [&::-webkit-details-marker]:hidden sm:flex-row sm:items-start">
                <span class="flex min-w-0 items-start gap-2">
                    <x-lucide-chevron-right class="mt-0.5 size-4 shrink-0 text-muted-foreground transition-transform group-open:rotate-90" />
                    <span class="min-w-0">
                        <span class="block font-semibold">{{ $academicLevel->name }}</span>
                        <span class="block text-sm text-muted-foreground">{{ $levelSummary }}</span>
                    </span>
                </span>

                <div x-on:click.stop class="ml-auto flex w-full shrink-0 flex-wrap items-center justify-end gap-1 sm:w-auto sm:justify-start">
                    @can('update', $academicLevel)
                        @if ($levelIndex > 0)
                            <button type="button" wire:click="moveLevel({{ $academicLevel->id }}, 'up')" wire:loading.attr="disabled" class="inline-flex size-7 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground disabled:opacity-50" aria-label="Move {{ $academicLevel->name }} up" title="Move up">
                                <x-lucide-chevron-up class="size-4" />
                            </button>
                        @endif
                        @if ($levelIndex < $levels->count() - 1)
                            <button type="button" wire:click="moveLevel({{ $academicLevel->id }}, 'down')" wire:loading.attr="disabled" class="inline-flex size-7 items-center justify-center rounded-md text-muted-foreground hover:bg-muted hover:text-foreground disabled:opacity-50" aria-label="Move {{ $academicLevel->name }} down" title="Move down">
                                <x-lucide-chevron-down class="size-4" />
                            </button>
                        @endif
                    @endcan
                    @can('create', \App\Models\AcademicLevel::class)
                        <april:button-link href="{{ route('academic-levels.create', ['parent_id' => $academicLevel->id] + $setupParameters) }}" variant="outline" size="sm">Add class</april:button-link>
                    @endcan
                    @if ($children->isEmpty())
                        @can('create', \App\Models\AcademicCycleSection::class)
                            <april:button-link href="{{ route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id] + $setupParameters) }}" variant="outline" size="sm">Add section</april:button-link>
                        @endcan
                    @endif
                    @can('view', $academicLevel)
                        <april:button-link href="{{ route('academic-levels.show', $academicLevel) }}" variant="ghost" size="sm">View level</april:button-link>
                    @endcan
                </div>
            </summary>

                @if ($hasChildren)
                    <div class="ml-2 mt-3 w-full min-w-0 space-y-2 border-l pl-3 sm:ml-4 sm:pl-4">
                        @if ($children->isNotEmpty())
                            @include('pages.academic-year.partials.level-tree', [
                                'levels' => $children,
                                'childrenByParent' => $childrenByParent,
                                'sectionsByLevel' => $sectionsByLevel,
                                'academicYear' => $academicYear,
                                'schoolSetup' => $schoolSetup,
                            ])
                        @endif

                        @foreach ($sections as $sectionIndex => $section)
                            @php
                                $sectionDetails = collect([
                                    $section->stream ? 'Stream: '.$section->stream : null,
                                    $section->shift ? 'Shift: '.$section->shift : null,
                                    $section->room ? 'Room: '.$section->room : null,
                                    $section->capacity !== null ? 'Capacity: '.$section->capacity : null,
                                    $section->language ? 'Language: '.$section->language : null,
                                    $section->homeroomTeacher?->name ? 'Teacher: '.$section->homeroomTeacher->name : null,
                                ])->filter()->join(' · ');
                            @endphp
                            <div wire:key="academic-cycle-section-{{ $section->id }}" class="flex min-w-0 flex-col gap-2 rounded-md bg-muted/30 px-3 py-2 text-sm sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-start gap-2">
                                    <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-muted-foreground"></span>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                            @can('view', $section)
                                                <a href="{{ route('academic-cycle-sections.show', $section) }}" class="font-medium hover:underline">{{ $section->name }}</a>
                                            @else
                                                <span class="font-medium">{{ $section->name }}</span>
                                            @endcan
                                            @if ($section->label && $section->label !== $section->name)
                                                <span class="text-xs text-muted-foreground">{{ $section->label }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-muted-foreground">{{ $sectionDetails ?: 'No additional details yet' }}</p>
                                    </div>
                                </div>
                                <div class="flex w-full shrink-0 items-center justify-end gap-1 border-t pt-2 sm:w-auto sm:justify-start sm:border-t-0 sm:pt-0">
                                    <x-academic-structure-status :status="$section->status" />
                                    @can('update', $section)
                                        @if ($section->isEditable())
                                            @if ($sectionIndex > 0)
                                                <button type="button" wire:click="moveSection({{ $section->id }}, 'up')" wire:loading.attr="disabled" class="inline-flex size-7 items-center justify-center rounded-md text-muted-foreground hover:bg-background hover:text-foreground disabled:opacity-50" aria-label="Move {{ $section->name }} up" title="Move up">
                                                    <x-lucide-chevron-up class="size-4" />
                                                </button>
                                            @endif
                                            @if ($sectionIndex < $sections->count() - 1)
                                                <button type="button" wire:click="moveSection({{ $section->id }}, 'down')" wire:loading.attr="disabled" class="inline-flex size-7 items-center justify-center rounded-md text-muted-foreground hover:bg-background hover:text-foreground disabled:opacity-50" aria-label="Move {{ $section->name }} down" title="Move down">
                                                    <x-lucide-chevron-down class="size-4" />
                                                </button>
                                            @endif
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="ml-2 mt-3 w-full min-w-0 border-l pl-3 text-sm sm:ml-4 sm:pl-4">
                        <span class="text-muted-foreground">No section added for this year yet</span>
                    </div>
                @endif
        </details>
    </div>
@endforeach
