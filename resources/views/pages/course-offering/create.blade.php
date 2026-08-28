@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => 'Course offerings'],
    ['href' => route('course-offerings.create'), 'text' => request()->boolean('setup') ? 'Add subject to this year' : 'Create', 'active'],
]])

@section('title', request()->boolean('setup') ? __('Add subject to this year') : __('Create course offering'))
@section('page_heading', request()->boolean('setup') ? __('Add subject to this year') : __('Create course offering'))

@section('content')
    <april:card>
        <slot:title class="flex items-center gap-1">
            <span>Add a subject to this year</span>
            <x-help-tooltip label="Subject setup help">A subject is what learners study, such as Mathematics, English, or Science. This step connects it to a class and reporting period for the selected school year. It is saved for review before it is activated.</x-help-tooltip>
        </slot:title>
        <slot:description>Choose the subject, class, period, and learners for this year.</slot:description>
        <slot:content>
            <form method="POST" action="{{ route('course-offerings.store') }}" class="space-y-6" x-data="{ rosterMode: @js(old('roster_mode', \App\Enums\RosterMode::HomeSection->value)) }">
                @csrf
                @if (request()->boolean('setup'))
                    <input type="hidden" name="setup" value="1">
                @endif
                <x-display-validation-errors />

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <april:label for="academic-year">{{ school_term('academic_year', 'School year') }}</april:label>
                        <select id="academic-year" name="academic_year_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a {{ strtolower(school_term('academic_year', 'school year')) }}</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" {{ (string) old('academic_year_id', request('academic_year_id')) === (string) $academicYear->id ? 'selected' : '' }}>{{ $academicYear->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-1"><april:label for="roster-mode">Who attends</april:label><x-help-tooltip label="Roster help">Use one section by default. The school year’s teaching setup may allow more options.</x-help-tooltip></div>
                        <select id="roster-mode" name="roster_mode" x-model="rosterMode" class="rounded-md border border-input bg-background px-3 py-2" required>
                            @foreach ($rosterModes as $rosterMode)
                                <option value="{{ $rosterMode->value }}">{{ school_roster_label($rosterMode) }}</option>
                            @endforeach
                        </select>
                        @if (!in_array(\App\Enums\RosterMode::CombinedHomeSections, $rosterModes, true))
                            <p class="text-sm text-muted-foreground">Combined sections is not available for this year’s teaching approach. Use one section or the whole level.</p>
                        @endif

                    </div>
                    <div x-cloak x-show="rosterMode === 'home_section' || rosterMode === 'combined_home_sections'" class="w-full md:col-span-2">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-1"><april:label for="cycle-sections">Participating sections</april:label><x-help-tooltip label="Participating sections help">For one section, choose exactly one. For combined sections, choose two or more sections from the selected school year and class.</x-help-tooltip></div>
                            <april:select id="cycle-sections" name="academic_cycle_section_ids[]" multiple placeholder="Select sections">
                                @foreach ($academicCycleSections as $academicCycleSection)
                                    <option value="{{ $academicCycleSection->id }}" @selected(in_array($academicCycleSection->id, old('academic_cycle_section_ids', [])))>
                                        {{ $academicCycleSection->academicYear->name }} · {{ $academicCycleSection->academicLevel->name }} · {{ $academicCycleSection->label ?? $academicCycleSection->name }}
                                    </option>
                                @endforeach
                            </april:select>
                            <p x-show="rosterMode === 'home_section'" class="text-sm text-muted-foreground">Choose one section.</p>
                            <p x-show="rosterMode === 'combined_home_sections'" class="text-sm text-muted-foreground">Choose at least two sections to teach together.</p>
                        </div>
                    </div>
                    <div x-cloak x-show="rosterMode === 'academic_level'" class="w-full md:col-span-2 rounded-md border border-primary/30 bg-primary/5 p-3 text-sm">
                        <p class="font-medium">Whole academic level</p>
                        <p class="mt-1 text-muted-foreground">Every learner in the selected class level is included. You do not need to choose sections or named learners.</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-1"><april:label for="academic-period">{{ school_term('period', 'Academic period') }}</april:label><x-help-tooltip label="Offering period help">Choose one period, or create this subject for every period in the selected school year.</x-help-tooltip></div>
                        <select id="academic-period" name="academic_period_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a {{ strtolower(school_term('period', 'period')) }}</option>
                            <option value="all" {{ old('academic_period_id') === 'all' ? 'selected' : '' }}>All {{ strtolower(school_terms('period', 'periods')) }} in the {{ strtolower(school_term('academic_year', 'school year')) }}</option>
                            @foreach ($academicYears as $academicYear)
                                @foreach ($academicYear->topLevelPeriods as $academicPeriod)
                                    <option value="{{ $academicPeriod->id }}" {{ (string) old('academic_period_id') === (string) $academicPeriod->id ? 'selected' : '' }}>{{ $academicYear->name }} · {{ $academicPeriod->display_name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-1">
                            <april:label for="subject">{{ school_term('course', 'Subject') }}</april:label>
                            <x-help-tooltip label="What is a subject?">A subject is the reusable name in the school catalog, such as Mathematics, English, or Science.</x-help-tooltip>
                        </div>
                        <select id="subject" name="subject_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a {{ strtolower(school_term('course', 'subject')) }}</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ (string) old('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @if ($subjects->isEmpty())
                            <div class="rounded-md border border-amber-500/30 bg-amber-500/10 p-3 text-sm">
                                <p>No subjects have been created yet.</p>
                                <april:button-link href="{{ route('subjects.create', array_filter(['setup' => request()->boolean('setup') ? 1 : null, 'academic_year_id' => request('academic_year_id')])) }}" variant="link" size="none" class="mt-1 gap-1 p-0">Create a subject first <span aria-hidden="true">→</span></april:button-link>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2">
                        <april:label for="academic-level">{{ school_term('class_level', 'Class') }}</april:label>
                        <select id="academic-level" name="academic_level_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a level</option>
                            @foreach ($academicLevels as $academicLevel)
                                <option value="{{ $academicLevel->id }}" {{ (string) old('academic_level_id') === (string) $academicLevel->id ? 'selected' : '' }}>{{ $academicLevel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <april:input-group id="planned-periods-per-week" name="planned_periods_per_week" type="number" min="1" max="80" label="Planned periods each week" value="{{ old('planned_periods_per_week') }}" />
                    <april:input-group id="capacity" name="capacity" type="number" min="1" max="5000" label="Capacity (optional)" value="{{ old('capacity') }}" />
                </div>

                <div x-cloak x-show="rosterMode === 'individual_roster'" class="flex flex-col gap-2">
                    <div class="flex items-center gap-1"><april:label for="student-records">Named learners</april:label><x-help-tooltip label="Named learners help">Use this when you need to choose learners one by one. Each learner must attend the selected class.</x-help-tooltip></div>
                    <april:select id="student-records" name="student_record_ids[]" multiple placeholder="Select learners">
                        @foreach ($studentRecords as $studentRecord)
                            <option value="{{ $studentRecord->id }}" @selected(in_array($studentRecord->id, old('student_record_ids', [])))>{{ $studentRecord->user?->name ?? $studentRecord->admission_number }} · {{ $studentRecord->academicCycleSection?->academicLevel?->name }}</option>
                        @endforeach
                    </april:select>
                </div>

                <div class="space-y-1">
                    <april:button type="submit">Save subject for this year</april:button>
                    <p class="text-sm text-muted-foreground">This saves the setup for review. It can be activated when the period is ready.</p>
                </div>
            </form>
        </slot:content>
    </april:card>
@endsection
