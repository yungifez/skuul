@foreach ($levels->values() as $levelIndex => $academicLevel)
    @php
        $children = $childrenByParent->get($academicLevel->id, collect());
        $sections = $sectionsByLevel->get($academicLevel->id, collect())->values();
        $offerings = $courseOfferingsByLevel->get($academicLevel->id, collect())->values();
        $singleSectionOfferingsBySection = $offerings
            ->filter(fn ($offering): bool => $offering->roster_mode === \App\Enums\RosterMode::HomeSection && $offering->cycleSections->count() === 1)
            ->groupBy(fn ($offering): int => (int) $offering->cycleSections->first()->id);
        $levelOfferings = $offerings->reject(fn ($offering): bool => $offering->roster_mode === \App\Enums\RosterMode::HomeSection && $offering->cycleSections->count() === 1)->values();
        $levelDepth = $levelDepth ?? 0;
        $setupParameters = $setupLinks ? ['setup' => 1] : [];

        if ($academicYear !== null) {
            $setupParameters['academic_year_id'] = $academicYear->id;
        }

        if ($schoolSetup) {
            $setupParameters['school_setup'] = 1;
        }

        $holdsSections = $academicYear !== null && !$academicLevel->is_group && $children->isEmpty();
        $isExpandable = $children->isNotEmpty() || $sections->isNotEmpty() || $offerings->isNotEmpty() || $holdsSections;
        $sectionCountLabel = $sections->count().' '.($sections->count() === 1 ? strtolower(school_term('section', 'section')) : strtolower(school_terms('section', 'sections')));
        $summary = collect([
            $children->isNotEmpty() ? $children->count().' '.($children->count() === 1 ? 'level' : 'levels') : null,
            $academicLevel->is_group ? 'can be taught together' : null,
            $sections->isNotEmpty() ? $sectionCountLabel : null,
            $offerings->isNotEmpty() ? $offerings->count().' '.($offerings->count() === 1 ? 'offering' : 'offerings') : null,
        ])->filter()->join(' · ');

        $user = auth()->user();
        $canUpdateLevel = $showLevelActions && $user->can('update', $academicLevel);
        $levelMenu = [
            'addSection' => $showLevelActions && $holdsSections && $user->can('create', \App\Models\AcademicCycleSection::class),
            'addChild' => $showLevelActions && $user->can('create', \App\Models\AcademicLevel::class),
            'view' => $showLevelActions && $user->can('view', $academicLevel),
            'edit' => $canUpdateLevel && $academicLevel->isEditable(),
            'moveUp' => $canUpdateLevel && $levelIndex > 0,
            'moveDown' => $canUpdateLevel && $levelIndex < $levels->count() - 1,
            'delete' => $showLevelActions && $user->can('delete', $academicLevel),
        ];
        $levelTerm = strtolower(school_term('class_level', 'class'));
        $sectionTerm = strtolower(school_term('section', 'section'));
    @endphp

    <div
        wire:key="academic-level-{{ $academicLevel->id }}"
        x-data="{ open: true }"
        @if ($levelDepth > 0) x-init="if (window.matchMedia('(max-width: 639px)').matches) open = false" @endif
        class="min-w-0"
    >
        <div class="flex min-h-11 min-w-0 items-center gap-1">
            <button
                type="button"
                @if ($isExpandable) x-on:click="open = !open" x-bind:aria-expanded="open.toString()" @else disabled @endif
                class="flex min-h-11 min-w-0 flex-1 select-none flex-wrap items-center gap-x-2 gap-y-0.5 rounded-md py-1.5 pr-2 text-left enabled:cursor-pointer enabled:hover:bg-muted/50 disabled:cursor-default"
            >
                <x-lucide-chevron-right class="size-4 shrink-0 text-muted-foreground transition-transform {{ $isExpandable ? '' : 'invisible' }}" x-bind:class="open && 'rotate-90'" />
                <span @class(['min-w-0 break-words', 'font-semibold' => $levelDepth === 0, 'font-medium' => $levelDepth > 0])>{{ $academicLevel->name }}</span>
                @if ($academicLevel->code)
                    <span class="text-xs text-muted-foreground">{{ $academicLevel->code }}</span>
                @endif
                @if ($academicLevel->is_group)
                    <span class="text-xs text-muted-foreground">Group</span>
                @endif
                @if ($academicLevel->status !== \App\Enums\AcademicStructureStatus::Active)
                    <x-academic-structure-status :status="$academicLevel->status" />
                @endif
                @if ($summary !== '')
                    <span class="basis-full pl-6 text-sm text-muted-foreground sm:ml-auto sm:basis-auto sm:pl-0">{{ $summary }}</span>
                @endif
            </button>

            @if (in_array(true, $levelMenu, true))
                <april:dropdown-menu>
                    <slot:trigger>
                        <april:button type="button" variant="ghost" size="icon" class="size-10 text-muted-foreground" aria-label="Actions for {{ $academicLevel->name }}">
                            <x-lucide-ellipsis class="size-4" />
                        </april:button>
                    </slot:trigger>
                    <slot:content class="w-56">
                        @if ($levelMenu['addSection'])
                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id] + $setupParameters) }}'">
                                <x-lucide-plus class="mr-2 size-4" />Add {{ $sectionTerm }}
                            </april:dropdown-menu-item>
                        @endif
                        @if ($levelMenu['addChild'])
                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('academic-levels.create', ['parent_id' => $academicLevel->id] + $setupParameters) }}'" aria-label="Add {{ with_indefinite_article($levelTerm) }} under {{ $academicLevel->name }}">
                                <x-lucide-folder-plus class="mr-2 size-4" />Add {{ $levelTerm }} inside
                            </april:dropdown-menu-item>
                        @endif
                        @if ($levelMenu['view'])
                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('academic-levels.show', $academicLevel) }}'">
                                <x-lucide-eye class="mr-2 size-4" />View
                            </april:dropdown-menu-item>
                        @endif
                        @if ($levelMenu['edit'])
                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('academic-levels.edit', $academicLevel) }}'">
                                <x-lucide-pencil class="mr-2 size-4" />Edit
                            </april:dropdown-menu-item>
                        @endif
                        @if ($levelMenu['moveUp'] || $levelMenu['moveDown'])
                            <april:dropdown-menu-separator />
                        @endif
                        @if ($levelMenu['moveUp'])
                            <april:dropdown-menu-item wire:click="moveLevel({{ $academicLevel->id }}, 'up')" aria-label="Move {{ $academicLevel->name }} up">
                                <x-lucide-arrow-up class="mr-2 size-4" />Move up
                            </april:dropdown-menu-item>
                        @endif
                        @if ($levelMenu['moveDown'])
                            <april:dropdown-menu-item wire:click="moveLevel({{ $academicLevel->id }}, 'down')" aria-label="Move {{ $academicLevel->name }} down">
                                <x-lucide-arrow-down class="mr-2 size-4" />Move down
                            </april:dropdown-menu-item>
                        @endif
                        @if ($levelMenu['delete'])
                            <april:dropdown-menu-separator />
                            <april:dropdown-menu-item class="text-destructive" wire:click="deleteLevel({{ $academicLevel->id }})" wire:confirm="Delete {{ $academicLevel->name }}? This only works when it has no child levels, sections, subjects, or teaching setup." aria-label="Delete {{ $academicLevel->name }}">
                                <x-lucide-trash-2 class="mr-2 size-4" />Delete
                            </april:dropdown-menu-item>
                        @endif
                    </slot:content>
                </april:dropdown-menu>
            @endif
        </div>

        @if ($isExpandable)
            <div x-show="open" class="mb-2 ml-2 min-w-0 border-l pl-3 sm:pl-4">
                @if ($children->isNotEmpty())
                    @include('pages.academic-year.partials.level-tree', [
                        'levels' => $children,
                        'childrenByParent' => $childrenByParent,
                        'sectionsByLevel' => $sectionsByLevel,
                        'courseOfferingsByLevel' => $courseOfferingsByLevel,
                        'academicYear' => $academicYear,
                        'schoolSetup' => $schoolSetup,
                        'setupLinks' => $setupLinks,
                        'showLevelActions' => $showLevelActions,
                        'levelDepth' => $levelDepth + 1,
                    ])
                @endif

                @if ($holdsSections && $sections->isEmpty())
                    <p class="flex min-h-11 items-center gap-2 text-sm text-muted-foreground">
                        No {{ strtolower(school_terms('section', 'sections')) }} this year
                        @if ($levelMenu['addSection'])
                            <a href="{{ route('academic-cycle-sections.create', ['academic_level_id' => $academicLevel->id] + $setupParameters) }}" class="select-none font-medium text-foreground underline-offset-4 hover:underline" aria-label="Add {{ with_indefinite_article($sectionTerm) }} under {{ $academicLevel->name }}">Add {{ $sectionTerm }}</a>
                        @endif
                    </p>
                @endif

                @foreach ($sections as $sectionIndex => $section)
                    @php
                        $sectionOfferings = $singleSectionOfferingsBySection->get($section->id, collect());
                        $sectionExtras = collect([$section->stream, $section->shift, $section->language])->filter()->join(' · ');
                        $canMoveSection = $showLevelActions && $section->isEditable() && $user->can('update', $section);
                        $sectionMenu = [
                            'edit' => $showLevelActions && $section->isEditable() && $user->can('update', $section),
                            'moveUp' => $canMoveSection && $sectionIndex > 0,
                            'moveDown' => $canMoveSection && $sectionIndex < $sections->count() - 1,
                        ];
                    @endphp
                    <div wire:key="academic-cycle-section-{{ $section->id }}" class="grid min-h-11 min-w-0 grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-0.5 py-1 text-sm sm:grid-cols-[minmax(0,1fr)_minmax(0,13rem)_6rem_4.5rem_2.5rem]">
                        <div class="flex min-w-0 flex-wrap items-baseline gap-x-2">
                            @can('view', $section)
                                <a href="{{ route('academic-cycle-sections.show', $section) }}" class="break-words font-medium hover:underline">{{ $section->name }}</a>
                            @else
                                <span class="break-words font-medium">{{ $section->name }}</span>
                            @endcan
                            @if ($section->label && $section->label !== $section->name)
                                <span class="text-xs text-muted-foreground">{{ $section->label }}</span>
                            @endif
                            @if ($sectionExtras !== '')
                                <span class="text-xs text-muted-foreground">{{ $sectionExtras }}</span>
                            @endif
                            @if ($section->status !== \App\Enums\AcademicStructureStatus::Active)
                                <span class="self-center"><x-academic-structure-status :status="$section->status" /></span>
                            @endif
                        </div>

                        <div class="flex items-center justify-end sm:col-start-5 sm:row-start-1">
                            @if (in_array(true, $sectionMenu, true))
                                <april:dropdown-menu>
                                    <slot:trigger>
                                        <april:button type="button" variant="ghost" size="icon" class="size-10 text-muted-foreground" aria-label="Actions for {{ $section->name }}">
                                            <x-lucide-ellipsis class="size-4" />
                                        </april:button>
                                    </slot:trigger>
                                    <slot:content class="w-48">
                                        @if ($sectionMenu['edit'])
                                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('academic-cycle-sections.edit', $section) }}'">
                                                <x-lucide-pencil class="mr-2 size-4" />Edit
                                            </april:dropdown-menu-item>
                                        @endif
                                        @if ($sectionMenu['moveUp'])
                                            <april:dropdown-menu-item wire:click="moveSection({{ $section->id }}, 'up')" aria-label="Move {{ $section->name }} up">
                                                <x-lucide-arrow-up class="mr-2 size-4" />Move up
                                            </april:dropdown-menu-item>
                                        @endif
                                        @if ($sectionMenu['moveDown'])
                                            <april:dropdown-menu-item wire:click="moveSection({{ $section->id }}, 'down')" aria-label="Move {{ $section->name }} down">
                                                <x-lucide-arrow-down class="mr-2 size-4" />Move down
                                            </april:dropdown-menu-item>
                                        @endif
                                    </slot:content>
                                </april:dropdown-menu>
                            @endif
                        </div>

                        <div class="col-span-2 flex min-w-0 flex-wrap gap-x-4 gap-y-0.5 text-muted-foreground sm:contents">
                            <span class="flex min-w-0 items-center gap-1.5" title="Class teacher">
                                <x-lucide-user class="size-3.5 shrink-0" />
                                @if ($section->homeroomTeacher)
                                    <span class="min-w-0 break-words text-foreground sm:truncate">{{ $section->homeroomTeacher->name }}</span>
                                @else
                                    <span>No teacher</span>
                                @endif
                            </span>
                            <span class="flex items-center gap-1.5" title="Room">
                                <x-lucide-door-open class="size-3.5 shrink-0" />
                                <span @class(['text-foreground' => filled($section->room)])>{{ $section->room ?: '—' }}</span>
                            </span>
                            <span class="flex items-center gap-1.5 sm:justify-end" title="Capacity">
                                <x-lucide-users class="size-3.5 shrink-0" />
                                <span @class(['tabular-nums', 'text-foreground' => $section->capacity !== null])>{{ $section->capacity ?? '—' }}</span>
                            </span>
                        </div>
                    </div>
                    @if ($sectionOfferings->isNotEmpty())
                        <div class="ml-2 border-l pl-3 sm:pl-4">
                            @include('pages.academic-year.partials.offering-tree', [
                                'offerings' => $sectionOfferings,
                            ])
                        </div>
                    @endif
                @endforeach

                @include('pages.academic-year.partials.offering-tree', [
                    'offerings' => $levelOfferings,
                ])
            </div>
        @endif
    </div>
@endforeach
