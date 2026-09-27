<div class="mx-auto flex w-full max-w-3xl flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:opacity-50';
        $sectionTerm = strtolower(school_term('section', 'section'));
        $sectionsTerm = strtolower(school_terms('section', 'sections'));
        $isGroup = $selectedLevel?->is_group ?? false;
        $choosesSections = !$isGroup && in_array($rosterMode, [\App\Enums\RosterMode::HomeSection->value, \App\Enums\RosterMode::CombinedHomeSections->value], true);
    @endphp

    <form wire:submit="save" class="flex flex-col gap-6" aria-label="Add a subject to this year">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="flex flex-col gap-2">
                <label for="subject" class="text-sm font-medium">{{ school_term('course', 'Subject') }}</label>
                <select id="subject" wire:model="subjectId" class="{{ $controlClasses }}" {{ field_error_bindings('subjectId') }}>
                    <option value="">Choose</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                @if ($subjects->isEmpty())
                    <a href="{{ route('subjects.create', array_filter(['setup' => $setup ? 1 : null, 'academic_year_id' => $academicYearId])) }}" class="text-sm font-medium underline underline-offset-4">Create a subject first</a>
                @endif
                <x-field-error name="subjectId" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="academic-year" class="text-sm font-medium">{{ school_term('academic_year', 'School year') }}</label>
                <select id="academic-year" wire:model.live="academicYearId" class="{{ $controlClasses }}" {{ field_error_bindings('academicYearId') }}>
                    <option value="">Choose</option>
                    @foreach ($academicYears as $academicYear)
                        <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                    @endforeach
                </select>
                <x-field-error name="academicYearId" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="academic-level" class="text-sm font-medium">{{ school_term('class_level', 'Class') }}</label>
                <select id="academic-level" wire:model.live="academicLevelId" class="{{ $controlClasses }}" {{ field_error_bindings('academicLevelId') }}>
                    <option value="">Choose</option>
                    <optgroup label="{{ school_terms('class_level', 'Classes') }}">
                        @foreach ($academicLevels->where('is_group', false) as $academicLevel)
                            <option value="{{ $academicLevel->id }}">{{ $academicLevel->name }}</option>
                        @endforeach
                    </optgroup>
                    @if ($academicLevels->where('is_group', true)->isNotEmpty())
                        <optgroup label="Groups">
                            @foreach ($academicLevels->where('is_group', true) as $academicLevel)
                                <option value="{{ $academicLevel->id }}">{{ $academicLevel->name }}</option>
                            @endforeach
                        </optgroup>
                    @endif
                </select>
                <x-field-error name="academicLevelId" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="academic-period" class="text-sm font-medium">{{ school_term('period', 'Academic period') }}</label>
                <select id="academic-period" wire:model="academicPeriodId" class="{{ $controlClasses }}" @disabled($selectedYear === null) {{ field_error_bindings('academicPeriodId') }}>
                    <option value="">Choose</option>
                    <option value="all">All {{ strtolower(school_terms('period', 'periods')) }} in the {{ strtolower(school_term('academic_year', 'school year')) }}</option>
                    @foreach ($selectedYear?->topLevelPeriods ?? [] as $academicPeriod)
                        <option value="{{ $academicPeriod->id }}">{{ $academicPeriod->display_name }}</option>
                    @endforeach
                </select>
                <x-field-error name="academicPeriodId" />
            </div>
        </div>

        <fieldset class="flex flex-col gap-3">
            <legend class="mb-2 text-sm font-medium">Who attends</legend>
            @if ($isGroup)
                <p class="text-sm">Everyone in {{ $selectedLevel->name }}</p>
            @else
                <div>
                    <label for="roster-mode" class="sr-only">Who attends</label>
                    <select id="roster-mode" wire:model.live="rosterMode" class="{{ $controlClasses }} sm:w-72" {{ field_error_bindings('rosterMode') }}>
                        @foreach ($rosterModes as $mode)
                            <option value="{{ $mode->value }}">{{ school_roster_label($mode) }}</option>
                        @endforeach
                    </select>
                </div>
                <x-field-error name="rosterMode" />

                @if ($choosesSections)
                    @if ($selectedLevel === null)
                        <p class="text-sm text-muted-foreground">Choose a {{ strtolower(school_term('class_level', 'class')) }} to see its {{ $sectionsTerm }}.</p>
                    @elseif ($sections->isEmpty())
                        <p class="text-sm text-muted-foreground">No {{ $sectionsTerm }} in {{ $selectedLevel->name }} this year.</p>
                    @else
                        <div class="flex flex-wrap gap-2" role="group" aria-label="{{ ucfirst($sectionsTerm) }}">
                            @foreach ($sections as $section)
                                <label wire:key="section-{{ $section->id }}" class="flex min-h-11 cursor-pointer items-center gap-2 rounded-md border px-3 text-sm select-none has-[:checked]:border-foreground">
                                    <input type="checkbox" value="{{ $section->id }}" wire:model="academicCycleSectionIds" class="size-4 rounded border-input">
                                    {{ $section->label ?? $section->name }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                    <x-field-error name="academicCycleSectionIds" />
                    <x-field-error name="academicCycleSectionIds.*" />
                @elseif ($rosterMode === \App\Enums\RosterMode::AcademicLevel->value)
                    <p class="text-sm">Everyone in {{ $selectedLevel?->name ?? 'the '.strtolower(school_term('class_level', 'class')) }}</p>
                @elseif ($rosterMode === \App\Enums\RosterMode::IndividualRoster->value)
                    @if ($learners->isEmpty())
                        <p class="text-sm text-muted-foreground">{{ $selectedLevel === null ? 'Choose a '.strtolower(school_term('class_level', 'class')).' to see its learners.' : 'No learners in '.$selectedLevel->name.' yet.' }}</p>
                    @else
                        <ul class="max-h-72 divide-y overflow-y-auto border-y" aria-label="Learners">
                            @foreach ($learners as $learner)
                                <li wire:key="learner-{{ $learner->id }}">
                                    <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm select-none">
                                        <input type="checkbox" value="{{ $learner->id }}" wire:model="studentRecordIds" class="size-4 rounded border-input">
                                        <span class="min-w-0 flex-1 truncate">{{ $learner->user?->name ?? $learner->admission_number }}</span>
                                        <span class="text-muted-foreground">{{ $learner->academicCycleSection?->label ?? $learner->academicCycleSection?->name }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <x-field-error name="studentRecordIds" />
                    <x-field-error name="studentRecordIds.*" />
                @endif
            @endif
        </fieldset>

        <div class="grid grid-cols-2 gap-4 sm:w-96">
            <div class="flex flex-col gap-2">
                <label for="planned-periods-per-week" class="text-sm font-medium">Periods a week</label>
                <input type="number" id="planned-periods-per-week" wire:model="plannedPeriodsPerWeek" min="1" max="80" class="{{ $controlClasses }}" {{ field_error_bindings('plannedPeriodsPerWeek') }}>
                <x-field-error name="plannedPeriodsPerWeek" />
            </div>
            <div class="flex flex-col gap-2">
                <label for="capacity" class="text-sm font-medium">Capacity</label>
                <input type="number" id="capacity" wire:model="capacity" min="1" max="5000" placeholder="No limit" class="{{ $controlClasses }}" {{ field_error_bindings('capacity') }}>
                <x-field-error name="capacity" />
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
            <april:button-link href="{{ $setup && $academicYearId ? route('academic-years.setup', [$academicYearId, 'subjects']) : route('course-offerings.index') }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Add subject</april:button>
        </div>
    </form>
</div>
