@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => 'Course offerings'],
    ['href' => route('course-offerings.create'), 'text' => 'Create', 'active'],
]])

@section('title', __('Create course offering'))
@section('page_heading', __('Create course offering'))

@section('content')
    <april:card>
        <slot:title class="flex items-center gap-1">
            <span>Create a draft offering</span>
            <x-help-tooltip label="Course offering help">Choose the subject, class, period, and learners. The teaching setup controls which roster choices are available.</x-help-tooltip>
        </slot:title>
        <slot:description>Choose what is taught, when, and to whom.</slot:description>
        <slot:content>
            <form method="POST" action="{{ route('course-offerings.store') }}" class="space-y-6">
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
                        <select id="roster-mode" name="roster_mode" class="rounded-md border border-input bg-background px-3 py-2" required>
                            @foreach (\App\Enums\RosterMode::cases() as $rosterMode)
                                <option value="{{ $rosterMode->value }}" {{ old('roster_mode', \App\Enums\RosterMode::HomeSection->value) === $rosterMode->value ? 'selected' : '' }}>{{ school_roster_label($rosterMode) }}</option>
                            @endforeach
                        </select>
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
                        <april:label for="subject">{{ school_term('course', 'Subject') }}</april:label>
                        <select id="subject" name="subject_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a {{ strtolower(school_term('course', 'subject')) }}</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ (string) old('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
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

                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-1"><april:label for="cycle-sections">{{ school_terms('section', 'Sections') }}</april:label><x-help-tooltip label="Offering sections help">Select one or more sections, or leave this empty for every learner in the selected class.</x-help-tooltip></div>
                    <select id="cycle-sections" name="academic_cycle_section_ids[]" multiple class="min-h-40 rounded-md border border-input bg-background px-3 py-2">
                        @foreach ($academicCycleSections as $academicCycleSection)
                            <option value="{{ $academicCycleSection->id }}" {{ in_array($academicCycleSection->id, old('academic_cycle_section_ids', [])) ? 'selected' : '' }}>
                                {{ $academicCycleSection->academicYear->name }} · {{ $academicCycleSection->academicLevel->name }} · {{ $academicCycleSection->label ?? $academicCycleSection->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-1"><april:label for="student-records">Named learners</april:label><x-help-tooltip label="Named learners help">Use this when you need to choose learners one by one. Each learner must attend the selected class.</x-help-tooltip></div>
                    <select id="student-records" name="student_record_ids[]" multiple class="min-h-40 rounded-md border border-input bg-background px-3 py-2">
                        @foreach ($studentRecords as $studentRecord)
                            <option value="{{ $studentRecord->id }}" {{ in_array($studentRecord->id, old('student_record_ids', [])) ? 'selected' : '' }}>{{ $studentRecord->user?->name ?? $studentRecord->admission_number }} · {{ $studentRecord->academicCycleSection?->academicLevel?->name }}</option>
                        @endforeach
                    </select>
                </div>

                <april:button type="submit">Create draft offering</april:button>
            </form>
        </slot:content>
    </april:card>
@endsection
