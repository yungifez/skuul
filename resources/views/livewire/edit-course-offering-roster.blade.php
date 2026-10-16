@php
    $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    $level = $courseOffering->academicLevel;
    $sectionsTerm = strtolower(school_terms('section', 'sections'));
    $choosesSections = in_array($rosterMode, [\App\Enums\RosterMode::HomeSection->value, \App\Enums\RosterMode::CombinedHomeSections->value], true);
@endphp
<form wire:submit="save" class="max-w-3xl space-y-6">
    <p class="text-sm text-muted-foreground">{{ $courseOffering->subject->name }} · {{ $courseOffering->academicYear->name }} · {{ $level->name }} · {{ $courseOffering->academicPeriod->display_name }}</p>

    <fieldset class="flex flex-col gap-3">
        <legend class="mb-2 text-sm font-medium">Who attends</legend>
        @if ($level->is_group)
            <p class="text-sm">Everyone in the classes under {{ $level->name }}</p>
        @else
            <div>
                <label for="roster-mode" class="sr-only">Who attends</label>
                <select id="roster-mode" wire:model.live="rosterMode" class="{{ $controlClasses }} sm:w-72" {{ field_error_bindings('rosterMode') }}>
                    @foreach ($rosterModes as $mode)
                        <option value="{{ $mode->value }}">{{ school_roster_label($mode) }}</option>
                    @endforeach
                </select>
            </div>

            @if ($choosesSections)
                @if ($sections->isEmpty())
                    <p class="text-sm text-muted-foreground">No {{ $sectionsTerm }} in {{ $level->name }} this year.</p>
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
                <p class="text-sm">Everyone in {{ $level->name }}</p>
            @elseif ($rosterMode === \App\Enums\RosterMode::IndividualRoster->value)
                @if ($learners->isEmpty())
                    <p class="text-sm text-muted-foreground">No learners in {{ $level->name }} yet.</p>
                @else
                    <ul class="max-h-96 divide-y overflow-y-auto border-y" aria-label="Learners">
                        @foreach ($learners as $learner)
                            <li wire:key="learner-{{ $learner->id }}">
                                <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm select-none">
                                    <input type="checkbox" value="{{ $learner->id }}" wire:model="studentRecordIds" class="size-4 rounded border-input">
                                    <span class="min-w-0 flex-1 truncate">{{ $learner->user?->name ?? $learner->admission_number }}</span>
                                    <span class="text-muted-foreground">{{ $learner->academicCycleSection?->label ?? $learner->academicCycleSection?->name ?? '—' }}</span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <x-field-error name="studentRecordIds" />
                <x-field-error name="studentRecordIds.*" />
            @endif
        @endif
        <x-field-error name="rosterMode" />
    </fieldset>

    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save roster</april:button>
</form>
