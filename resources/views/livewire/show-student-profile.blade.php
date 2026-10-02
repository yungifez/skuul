<div class="flex flex-col gap-10">
    @php
        $sectionTerm = strtolower(school_term('section', 'section'));
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:opacity-50';
        $isClosed = $studentRecord?->status->isClosed() ?? true;
    @endphp

    <livewire:show-user-profile :user="$student" />

    @if ($studentRecord)
        <section aria-labelledby="enrollment-heading" class="flex flex-col gap-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 id="enrollment-heading" class="text-base font-semibold">Enrollment</h2>
                    <x-enrollment-status :enrollment="$studentRecord" />
                </div>

                @if ($canManageEnrollment)
                    <april:dropdown-menu>
                        <slot:trigger>
                            <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="Change the enrollment">
                                <x-lucide-ellipsis class="size-4" />
                            </april:button>
                        </slot:trigger>
                        <slot:content>
                            <april:dropdown-menu-item wire:click="$set('managing', 'status')">
                                <x-lucide-refresh-cw class="mr-2 size-4" />Change status
                            </april:dropdown-menu-item>
                            <april:dropdown-menu-item wire:click="$set('managing', 'placement')">
                                <x-lucide-arrow-right-left class="mr-2 size-4" />Change placement
                            </april:dropdown-menu-item>
                            @if ($campusCycleSections !== [] && !$openCampusMoveRequest)
                                <april:dropdown-menu-item wire:click="$set('managing', 'campus')">
                                    <x-lucide-building-2 class="mr-2 size-4" />Move to another campus
                                </april:dropdown-menu-item>
                            @endif
                        </slot:content>
                    </april:dropdown-menu>
                @endif
            </div>

            <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4">
                @foreach ([
                    'Admission number' => $studentRecord->admission_number,
                    'Admitted' => $studentRecord->admission_date?->format('j M Y'),
                    school_term('class_level', 'Class') => $studentRecord->academicCycleSection?->academicLevel?->name,
                    school_term('section', 'Section') => $studentRecord->academicCycleSection?->label ?? $studentRecord->academicCycleSection?->name,
                ] as $label => $value)
                    <div class="min-w-0">
                        <dt class="text-sm text-muted-foreground">{{ $label }}</dt>
                        <dd @class(['truncate font-medium', 'text-muted-foreground' => blank($value)])>{{ filled($value) ? $value : '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($openCampusMoveRequest)
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" id="campus-move-waiting">
                    <p class="text-sm">
                        Waiting for {{ $openCampusMoveRequest->toSchool->name }}
                        @if ($openCampusMoveRequest->academicCycleSection)
                            · {{ $openCampusMoveRequest->academicCycleSection->label ?? $openCampusMoveRequest->academicCycleSection->name }}
                        @endif
                        · {{ $openCampusMoveRequest->effective_on?->format('j M Y') }}
                    </p>
                    @if ($canManageEnrollment)
                        <april:button type="button" variant="outline" class="h-11 w-full select-none sm:w-auto" wire:click="cancelCampusMove"
                            wire:confirm="Take the campus move request back?" wire:loading.attr="disabled" wire:target="cancelCampusMove">
                            Take the request back
                        </april:button>
                    @endif
                </div>
                <x-field-error name="campusCycleSectionId" />
            @endif

            @if ($canManageEnrollment && $managing === 'status')
                <form wire:submit="changeStatus" class="flex flex-col gap-3" aria-labelledby="status-form-heading">
                    <h3 id="status-form-heading" class="text-sm font-semibold">Change enrollment status</h3>
                    <div class="grid gap-3 sm:grid-cols-[12rem_10rem_1fr]">
                        <div>
                            <label for="status-selection" class="sr-only">New status</label>
                            <select id="status-selection" wire:model="statusSelection" class="{{ $controlClasses }}" {{ field_error_bindings('statusSelection') }}>
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status-effective-on" class="sr-only">Effective on</label>
                            <input type="date" id="status-effective-on" wire:model="statusEffectiveOn" max="{{ school_today()->toDateString() }}" class="{{ $controlClasses }}" {{ field_error_bindings('statusEffectiveOn') }}>
                        </div>
                        <div>
                            <label for="status-reason" class="sr-only">Reason</label>
                            <input id="status-reason" wire:model="statusReason" maxlength="1000" placeholder="Why (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('statusReason') }}>
                        </div>
                    </div>
                    <x-field-error name="statusSelection" />
                    <x-field-error name="statusEffectiveOn" />
                    <x-field-error name="statusReason" />
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="$set('managing', '')">Cancel</april:button>
                        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="changeStatus">Save status</april:button>
                    </div>
                </form>
            @endif

            @if ($canManageEnrollment && $managing === 'placement')
                <form wire:submit="changePlacement" class="flex flex-col gap-3" aria-labelledby="placement-form-heading">
                    <h3 id="placement-form-heading" class="text-sm font-semibold">
                        Change placement
                        @if ($academicYear)
                            <span class="font-normal text-muted-foreground">· {{ $academicYear->name }}@if ($academicPeriod) · {{ $academicPeriod->name }}@endif</span>
                        @endif
                    </h3>
                    @if (!$academicYear)
                        <p class="text-sm text-muted-foreground">Choose a working {{ strtolower(school_term('academic_year', 'school year')) }} first.</p>
                    @endif
                    <div class="grid gap-3 sm:grid-cols-[1fr_10rem_1fr]">
                        <div>
                            <label for="placement-cycle-section" class="sr-only">{{ school_term('section', 'Section') }}</label>
                            <select id="placement-cycle-section" wire:model="placementCycleSectionId" class="{{ $controlClasses }}" @disabled(!$academicYear || $isClosed) {{ field_error_bindings('placementCycleSectionId') }}>
                                <option value="">Choose a {{ $sectionTerm }}</option>
                                @foreach ($cycleSections as $cycleSection)
                                    <option value="{{ $cycleSection['id'] }}">{{ $cycleSection['level'] }} · {{ $cycleSection['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="placement-effective-on" class="sr-only">Effective on</label>
                            <input type="date" id="placement-effective-on" wire:model="placementEffectiveOn" max="{{ school_today()->toDateString() }}" class="{{ $controlClasses }}" {{ field_error_bindings('placementEffectiveOn') }}>
                        </div>
                        <div>
                            <label for="placement-reason" class="sr-only">Reason</label>
                            <input id="placement-reason" wire:model="placementReason" maxlength="1000" placeholder="Why (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('placementReason') }}>
                        </div>
                    </div>
                    <x-field-error name="placementCycleSectionId" />
                    <x-field-error name="placementEffectiveOn" />
                    <x-field-error name="placementReason" />
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="$set('managing', '')">Cancel</april:button>
                        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="changePlacement" :disabled="!$academicYear || $isClosed">Save placement</april:button>
                    </div>
                </form>
            @endif

            @if ($canManageEnrollment && $managing === 'campus' && $campusCycleSections !== [] && !$openCampusMoveRequest)
                <form wire:submit="moveCampus" class="flex flex-col gap-3" aria-labelledby="campus-form-heading">
                    <h3 id="campus-form-heading" class="text-sm font-semibold">Move to another campus</h3>
                    @unless ($movesCampusFreely)
                        <p class="text-sm text-muted-foreground">The receiving campus has to agree.</p>
                    @endunless
                    <div class="grid gap-3 sm:grid-cols-[1fr_10rem_1fr]">
                        <div>
                            <label for="campus-cycle-section" class="sr-only">Campus and {{ $sectionTerm }}</label>
                            <select id="campus-cycle-section" wire:model="campusCycleSectionId" class="{{ $controlClasses }}" @disabled($isClosed) {{ field_error_bindings('campusCycleSectionId') }}>
                                <option value="">Choose a campus {{ $sectionTerm }}</option>
                                @foreach ($campusCycleSections as $campusCycleSection)
                                    <option value="{{ $campusCycleSection['id'] }}">{{ $campusCycleSection['campus'] }} · {{ $campusCycleSection['level'] }} · {{ $campusCycleSection['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="campus-effective-on" class="sr-only">Effective on</label>
                            <input type="date" id="campus-effective-on" wire:model="campusEffectiveOn" max="{{ school_today()->toDateString() }}" class="{{ $controlClasses }}" {{ field_error_bindings('campusEffectiveOn') }}>
                        </div>
                        <div>
                            <label for="campus-reason" class="sr-only">Reason</label>
                            <input id="campus-reason" wire:model="campusReason" maxlength="1000" placeholder="Why (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('campusReason') }}>
                        </div>
                    </div>
                    <x-field-error name="campusCycleSectionId" />
                    <x-field-error name="campusEffectiveOn" />
                    <x-field-error name="campusReason" />
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="$set('managing', '')">Cancel</april:button>
                        <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="moveCampus" :disabled="$isClosed">{{ $movesCampusFreely ? 'Move campus' : 'Ask the other campus' }}</april:button>
                    </div>
                </form>
            @endif
        </section>

        <div class="grid gap-10 lg:grid-cols-2">
            <section aria-labelledby="status-history-heading" class="flex flex-col gap-3">
                <h2 id="status-history-heading" class="text-base font-semibold">Status history</h2>
                @if ($studentRecord->statusChanges->isNotEmpty())
                    <ol class="divide-y border-y">
                        @foreach ($studentRecord->statusChanges->sortByDesc('effective_on') as $change)
                            <li class="py-3 text-sm" wire:key="enrollment-status-change-{{ $change->id }}">
                                <p class="flex flex-wrap items-baseline justify-between gap-x-4">
                                    <span class="font-medium">{{ $change->from_status->label() }} → {{ $change->to_status->label() }}</span>
                                    <span class="text-muted-foreground">{{ $change->effective_on?->format('j M Y') }}</span>
                                </p>
                                <p class="text-muted-foreground">
                                    {{ $change->changedBy?->name ?? 'Unknown person' }}@if (filled($change->reason)) · {{ $change->reason }}@endif
                                </p>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="text-sm text-muted-foreground">No changes</p>
                @endif
            </section>

            <section aria-labelledby="placement-history-heading" class="flex flex-col gap-3">
                <h2 id="placement-history-heading" class="text-base font-semibold">Placement history</h2>
                @if ($studentRecord->placements->isNotEmpty())
                    <ol class="divide-y border-y">
                        @foreach ($studentRecord->placements->sortByDesc('effective_on') as $placement)
                            <li class="py-3 text-sm" wire:key="enrollment-placement-{{ $placement->id }}">
                                <p class="flex flex-wrap items-baseline justify-between gap-x-4">
                                    <span class="font-medium">
                                        {{ $placement->academicCycleSection?->academicLevel?->name ?? '—' }}
                                        · {{ $placement->academicCycleSection?->label ?? $placement->academicCycleSection?->name ?? '—' }}
                                    </span>
                                    <span class="text-muted-foreground">{{ $placement->effective_on?->format('j M Y') }}</span>
                                </p>
                                <p class="text-muted-foreground">
                                    {{ $placement->academicYear?->name ?? '—' }}@if ($placement->academicPeriod) · {{ $placement->academicPeriod->name }}@endif
                                </p>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="text-sm text-muted-foreground">No placements</p>
                @endif
            </section>
        </div>
    @else
        <p class="border-y py-4 text-sm text-muted-foreground">Not enrolled in this school.</p>
    @endif
</div>
