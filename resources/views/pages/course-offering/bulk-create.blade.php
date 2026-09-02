@php
    $selectedLevelIds = collect(old('level_ids', []))->map(fn ($id): string => (string) $id)->values()->all();
    $selectedSubjectId = (string) old('subject_id', '');
    $selectedPeriodId = (string) old('academic_period_id', '');
@endphp

@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => school_terms('course', 'Course').' being taught'],
    ['href' => route('course-offerings.bulk-create', ['academic_year_id' => $selectedAcademicYear->id]), 'text' => 'Set up across levels', 'active'],
]])

@section('title', 'Set up subjects across levels')
@section('page_heading', 'Set up subjects across levels')

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
        <april:card>
            <slot:title>Set up one subject for several levels</slot:title>
            <slot:description>This creates separate offerings for each selected class or group. A group can be taught as one scope across all of its child classes.</slot:description>
            <slot:content>
                <form method="POST" action="{{ route('course-offerings.bulk-store') }}" class="space-y-8" x-data="{ selectedLevels: @js($selectedLevelIds) }">
                    @csrf
                    @if (request()->boolean('setup'))
                        <input type="hidden" name="setup" value="1">
                    @endif
                    <input type="hidden" name="academic_year_id" value="{{ $selectedAcademicYear->id }}">
                    <x-display-validation-errors />

                    <div class="flex flex-wrap items-center gap-3 border-b pb-6">
                        <april:button type="submit">Create offerings for selected levels</april:button>
                        <april:button-link href="{{ request()->boolean('setup') ? route('academic-years.setup', [$selectedAcademicYear, 'subjects']) : route('course-offerings.index') }}" variant="ghost">Cancel</april:button-link>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="flex flex-col gap-2 md:col-span-2">
                            <april:label for="academic-year">School year</april:label>
                            <div id="academic-year" class="rounded-md border bg-muted/30 px-3 py-3">
                                <p class="font-medium">{{ $selectedAcademicYear->name }}</p>
                                <p class="mt-1 text-sm text-muted-foreground">The separate offerings will all be created for this school year. This bulk setup is scoped by the academic year in the link.</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <april:label for="subject">Subject</april:label>
                            <select id="subject" name="subject_id" class="w-full rounded-md border border-input bg-background px-3 py-2" required>
                                <option value="">Select a subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}" @selected($selectedSubjectId === (string) $subject->id)>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <april:label for="academic-period">Reporting period</april:label>
                            <select id="academic-period" name="academic_period_id" class="w-full rounded-md border border-input bg-background px-3 py-2" required>
                                <option value="">Select a period</option>
                                <option value="all" @selected($selectedPeriodId === 'all')>All periods in {{ $selectedAcademicYear->name }}</option>
                                @foreach ($selectedAcademicYear->topLevelPeriods as $academicPeriod)
                                    <option value="{{ $academicPeriod->id }}" @selected($selectedPeriodId === (string) $academicPeriod->id)>{{ $academicPeriod->display_name }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-muted-foreground">All periods creates one separate offering per level and period.</p>
                        </div>
                    </div>

                    <div class="space-y-4 border-t pt-6">
                        <div>
                            <h2 class="font-semibold">1. Choose classes or groups</h2>
                            <p class="text-sm text-muted-foreground">Select every class or group that teaches this subject. You will set each one’s details below.</p>
                        </div>
                        <div class="space-y-4">
                            @if ($academicLevels->where('is_group', false)->isNotEmpty())
                                <div class="space-y-2">
                                    <h3 class="text-sm font-medium">{{ school_terms('class_level', 'Classes') }}</h3>
                                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach ($academicLevels->where('is_group', false) as $academicLevel)
                                            <label class="flex cursor-pointer items-start gap-3 rounded-md border p-3 transition-colors hover:bg-accent/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
                                                <input type="checkbox" name="level_ids[]" value="{{ $academicLevel->id }}" x-model="selectedLevels" class="mt-0.5 size-4 shrink-0 rounded border-input" @checked(in_array((string) $academicLevel->id, $selectedLevelIds, true))>
                                                <span class="text-sm font-medium">{{ $academicLevel->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if ($academicLevels->where('is_group', true)->isNotEmpty())
                                <div class="space-y-2">
                                    <h3 class="text-sm font-medium">Groups <span class="font-normal text-muted-foreground">(whole-group teaching)</span></h3>
                                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach ($academicLevels->where('is_group', true) as $academicLevel)
                                            <label class="flex cursor-pointer items-start gap-3 rounded-md border p-3 transition-colors hover:bg-accent/40 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5">
                                                <input type="checkbox" name="level_ids[]" value="{{ $academicLevel->id }}" x-model="selectedLevels" class="mt-0.5 size-4 shrink-0 rounded border-input" @checked(in_array((string) $academicLevel->id, $selectedLevelIds, true))>
                                                <span class="text-sm">
                                                    <span class="block font-medium">{{ $academicLevel->name }}</span>
                                                    <span class="block text-xs text-muted-foreground">Includes its child classes</span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4 border-t pt-6">
                        <div>
                            <h2 class="font-semibold">2. Configure each selection</h2>
                            <p class="text-sm text-muted-foreground">These settings are copied only to the selected class or group’s offering.</p>
                        </div>

                        @foreach ($academicLevels as $academicLevel)
                            <template x-if="selectedLevels.includes('{{ $academicLevel->id }}')">
                                <section class="space-y-4 rounded-lg border p-4">
                                    <div>
                                        <h3 class="font-semibold">{{ $academicLevel->name }}</h3>
                                        <p class="text-sm text-muted-foreground">Configure how {{ $academicLevel->name }} receives this subject.</p>
                                    </div>
                                    <input type="hidden" name="configurations[{{ $academicLevel->id }}][academic_level_id]" value="{{ $academicLevel->id }}">

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div class="flex flex-col gap-2">
                                            <april:label for="roster-mode-{{ $academicLevel->id }}">Who attends</april:label>
                                            @if ($academicLevel->is_group)
                                                <input type="hidden" name="configurations[{{ $academicLevel->id }}][roster_mode]" value="{{ \App\Enums\RosterMode::AcademicLevel->value }}">
                                                <div id="roster-mode-{{ $academicLevel->id }}" class="rounded-md border border-primary/30 bg-primary/5 px-3 py-2 text-sm">Whole group</div>
                                                <p class="text-sm text-muted-foreground">Every learner in its child classes attends.</p>
                                            @else
                                                <select id="roster-mode-{{ $academicLevel->id }}" name="configurations[{{ $academicLevel->id }}][roster_mode]" class="rounded-md border border-input bg-background px-3 py-2" required>
                                                    @foreach ($rosterModes as $rosterMode)
                                                        <option value="{{ $rosterMode->value }}" @selected(old("configurations.{$academicLevel->id}.roster_mode", \App\Enums\RosterMode::HomeSection->value) === $rosterMode->value)>{{ school_roster_label($rosterMode) }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <april:input-group id="planned-periods-{{ $academicLevel->id }}" name="configurations[{{ $academicLevel->id }}][planned_periods_per_week]" type="number" min="1" max="80" label="Periods each week" value="{{ old("configurations.{$academicLevel->id}.planned_periods_per_week") }}" />
                                            <april:input-group id="capacity-{{ $academicLevel->id }}" name="configurations[{{ $academicLevel->id }}][capacity]" type="number" min="1" max="5000" label="Capacity" value="{{ old("configurations.{$academicLevel->id}.capacity") }}" />
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        <april:label for="sections-{{ $academicLevel->id }}">Participating sections</april:label>
                                        @if ($academicLevel->is_group)
                                            <p class="rounded-md border border-primary/30 bg-primary/5 p-3 text-sm text-muted-foreground">Whole-group teaching includes sections from all child classes.</p>
                                        @elseif ($academicCycleSections->where('academic_level_id', $academicLevel->id)->isEmpty())
                                            <p class="rounded-md border border-dashed p-3 text-sm text-muted-foreground">No sections have been created for {{ $academicLevel->name }} in this school year. You can use a whole-level roster or add sections first.</p>
                                        @else
                                            <april:select id="sections-{{ $academicLevel->id }}" name="configurations[{{ $academicLevel->id }}][academic_cycle_section_ids][]" multiple placeholder="Select sections">
                                                @foreach ($academicCycleSections->where('academic_level_id', $academicLevel->id) as $academicCycleSection)
                                                    <option value="{{ $academicCycleSection->id }}" @selected(in_array($academicCycleSection->id, old("configurations.{$academicLevel->id}.academic_cycle_section_ids", [])))>{{ $academicCycleSection->label ?? $academicCycleSection->name }}</option>
                                                @endforeach
                                            </april:select>
                                            <p class="text-sm text-muted-foreground">Choose sections for a one-section or combined-section roster. A whole-level roster ignores this list.</p>
                                        @endif
                                    </div>
                                </section>
                            </template>
                        @endforeach

                        <p x-show="selectedLevels.length === 0" x-cloak class="rounded-md border border-dashed p-4 text-sm text-muted-foreground">Select at least one level to configure the subject.</p>
                    </div>

                </form>
            </slot:content>
        </april:card>
    </div>
@endsection
