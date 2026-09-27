<div class="mx-auto flex w-full max-w-3xl flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $chipClasses = 'flex min-h-11 cursor-pointer items-center gap-2 rounded-md border px-3 text-sm select-none has-[:checked]:border-foreground has-[:checked]:font-medium';
        $sectionsTerm = strtolower(school_terms('section', 'sections'));
    @endphp

    <form wire:submit="save" class="flex flex-col gap-8" aria-label="Set up a subject across classes">
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="subject" class="sr-only">{{ school_term('course', 'Subject') }}</label>
                <select id="subject" wire:model="subjectId" class="{{ $controlClasses }}" {{ field_error_bindings('subjectId') }}>
                    <option value="">Choose a {{ strtolower(school_term('course', 'subject')) }}</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                <x-field-error name="subjectId" class="mt-1" />
            </div>
            <div>
                <label for="academic-period" class="sr-only">{{ school_term('period', 'Academic period') }}</label>
                <select id="academic-period" wire:model="academicPeriodId" class="{{ $controlClasses }}" {{ field_error_bindings('academicPeriodId') }}>
                    <option value="">Choose a {{ strtolower(school_term('period', 'period')) }}</option>
                    <option value="all">All {{ strtolower(school_terms('period', 'periods')) }} in {{ $academicYear->name }}</option>
                    @foreach ($academicYear->topLevelPeriods as $academicPeriod)
                        <option value="{{ $academicPeriod->id }}">{{ $academicPeriod->display_name }}</option>
                    @endforeach
                </select>
                <x-field-error name="academicPeriodId" class="mt-1" />
            </div>
        </div>

        <fieldset class="flex flex-col gap-4">
            <legend class="mb-3 text-base font-semibold">Who takes it</legend>
            @if ($classes->isNotEmpty())
                <div class="flex flex-wrap gap-2" role="group" aria-label="{{ school_terms('class_level', 'Classes') }}">
                    @foreach ($classes as $level)
                        <label wire:key="level-{{ $level->id }}" class="{{ $chipClasses }}">
                            <input type="checkbox" value="{{ $level->id }}" wire:model.live="levelIds" class="size-4 rounded border-input">
                            {{ $level->name }}
                        </label>
                    @endforeach
                </div>
            @endif
            @if ($groups->isNotEmpty())
                <div class="flex flex-wrap gap-2" role="group" aria-label="Groups">
                    @foreach ($groups as $level)
                        <label wire:key="level-{{ $level->id }}" class="{{ $chipClasses }}">
                            <input type="checkbox" value="{{ $level->id }}" wire:model.live="levelIds" class="size-4 rounded border-input">
                            {{ $level->name }}
                            <x-lucide-users class="size-4 text-muted-foreground" aria-label="Whole group" />
                        </label>
                    @endforeach
                </div>
            @endif
            @if ($classes->isEmpty() && $groups->isEmpty())
                <p class="text-sm text-muted-foreground">No {{ strtolower(school_terms('class_level', 'classes')) }}</p>
            @endif
            <x-field-error name="levelIds" />
            <x-field-error name="levelIds.*" />
        </fieldset>

        @if ($chosenLevels->isNotEmpty())
            <ul class="divide-y border-y" aria-label="Settings for each choice">
                @foreach ($chosenLevels as $level)
                    @php
                        $mode = $configurations[$level->id]['roster_mode'] ?? \App\Enums\RosterMode::HomeSection->value;
                        $levelSections = $sectionsByLevel->get($level->id, collect());
                        $choosesSections = !$level->is_group && \App\Enums\RosterMode::tryFrom($mode)?->usesHomeSections();
                    @endphp
                    <li wire:key="settings-{{ $level->id }}" class="flex flex-col gap-3 py-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h3 class="font-medium">{{ $level->name }}</h3>
                            @if ($level->is_group)
                                <p class="text-sm text-muted-foreground">Everyone in the group</p>
                            @else
                                <div class="w-full sm:w-64">
                                    <label for="roster-mode-{{ $level->id }}" class="sr-only">Who attends in {{ $level->name }}</label>
                                    <select id="roster-mode-{{ $level->id }}" wire:model.live="configurations.{{ $level->id }}.roster_mode" class="{{ $controlClasses }}">
                                        @foreach ($rosterModes as $rosterMode)
                                            <option value="{{ $rosterMode->value }}">{{ school_roster_label($rosterMode) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        @if ($choosesSections)
                            @if ($levelSections->isEmpty())
                                <p class="text-sm text-muted-foreground">No {{ $sectionsTerm }} this year</p>
                            @else
                                <div class="flex flex-wrap gap-2" role="group" aria-label="{{ ucfirst($sectionsTerm) }} in {{ $level->name }}">
                                    @foreach ($levelSections as $section)
                                        <label wire:key="section-{{ $section->id }}" class="{{ $chipClasses }}">
                                            <input type="checkbox" value="{{ $section->id }}" wire:model="configurations.{{ $level->id }}.section_ids" class="size-4 rounded border-input">
                                            {{ $section->label ?? $section->name }}
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        <div class="grid grid-cols-2 gap-3 sm:w-96">
                            <div>
                                <label for="planned-periods-{{ $level->id }}" class="sr-only">Periods a week in {{ $level->name }}</label>
                                <input type="number" id="planned-periods-{{ $level->id }}" min="1" max="80" placeholder="Periods a week" wire:model="configurations.{{ $level->id }}.planned_periods_per_week" class="{{ $controlClasses }}" {{ field_error_bindings('configurations.'.$level->id.'.planned_periods_per_week') }}>
                            </div>
                            <div>
                                <label for="capacity-{{ $level->id }}" class="sr-only">Capacity in {{ $level->name }}</label>
                                <input type="number" id="capacity-{{ $level->id }}" min="1" max="5000" placeholder="Capacity" wire:model="configurations.{{ $level->id }}.capacity" class="{{ $controlClasses }}" {{ field_error_bindings('configurations.'.$level->id.'.capacity') }}>
                            </div>
                        </div>
                        <x-field-error name="configurations.{{ $level->id }}.roster_mode" />
                        <x-field-error name="configurations.{{ $level->id }}.section_ids.*" />
                        <x-field-error name="configurations.{{ $level->id }}.planned_periods_per_week" />
                        <x-field-error name="configurations.{{ $level->id }}.capacity" />
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
            <april:button-link href="{{ $setup ? route('course-offerings.bulk-create', ['academic_year_id' => $academicYear->id, 'setup' => 1]) : route('course-offerings.index') }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Add subject</april:button>
        </div>
    </form>
</div>
