@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => 'Course offerings'],
    ['href' => route('course-offerings.create'), 'text' => 'Create', 'active'],
]])

@section('title', __('Create course offering'))
@section('page_heading', __('Create course offering'))

@section('content')
    <april:card>
        <slot:title>Create a draft offering</slot:title>
        <slot:description>Choose the subject, level, exact academic period, and roster. The campus teaching setup decides which roster types are allowed.</slot:description>
        <slot:content>
            <form method="POST" action="{{ route('course-offerings.store') }}" class="space-y-6">
                @csrf
                <x-display-validation-errors />

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <april:label for="academic-year">Academic cycle</april:label>
                        <select id="academic-year" name="academic_year_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a cycle</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" {{ (string) old('academic_year_id') === (string) $academicYear->id ? 'selected' : '' }}>{{ $academicYear->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <april:label for="roster-mode">Learner roster</april:label>
                        <select id="roster-mode" name="roster_mode" class="rounded-md border border-input bg-background px-3 py-2" required>
                            @foreach (\App\Enums\RosterMode::cases() as $rosterMode)
                                <option value="{{ $rosterMode->value }}" {{ old('roster_mode', \App\Enums\RosterMode::HomeSection->value) === $rosterMode->value ? 'selected' : '' }}>{{ $rosterMode->label() }}</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-muted-foreground">Use one home section by default. The cycle’s teaching setup may allow more options.</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <april:label for="academic-period">Academic period</april:label>
                        <select id="academic-period" name="academic_period_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a period</option>
                            @foreach ($academicYears as $academicYear)
                                @foreach ($academicYear->academicPeriods as $academicPeriod)
                                    <option value="{{ $academicPeriod->id }}" {{ (string) old('academic_period_id') === (string) $academicPeriod->id ? 'selected' : '' }}>{{ $academicYear->name }} · {{ $academicPeriod->display_name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <april:label for="subject">Subject</april:label>
                        <select id="subject" name="subject_id" class="rounded-md border border-input bg-background px-3 py-2" required>
                            <option value="">Select a subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ (string) old('subject_id') === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <april:label for="academic-level">Academic level</april:label>
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
                    <april:label for="cycle-sections">Home sections</april:label>
                    <select id="cycle-sections" name="academic_cycle_section_ids[]" multiple class="min-h-40 rounded-md border border-input bg-background px-3 py-2">
                        @foreach ($academicCycleSections as $academicCycleSection)
                            <option value="{{ $academicCycleSection->id }}" {{ in_array($academicCycleSection->id, old('academic_cycle_section_ids', [])) ? 'selected' : '' }}>
                                {{ $academicCycleSection->academicYear->name }} · {{ $academicCycleSection->academicLevel->name }} · {{ $academicCycleSection->label ?? $academicCycleSection->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-muted-foreground">Select one section, several sections, or none for a whole-level roster. Every selected section must belong to the chosen level and cycle.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <april:label for="student-records">Named learners</april:label>
                    <select id="student-records" name="student_record_ids[]" multiple class="min-h-40 rounded-md border border-input bg-background px-3 py-2">
                        @foreach ($studentRecords as $studentRecord)
                            <option value="{{ $studentRecord->id }}" {{ in_array($studentRecord->id, old('student_record_ids', [])) ? 'selected' : '' }}>{{ $studentRecord->user?->name ?? $studentRecord->admission_number }} · {{ $studentRecord->academicCycleSection?->academicLevel?->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-muted-foreground">Use this only for a named-learner roster. Learners must actively attend the selected academic level.</p>
                </div>

                <april:button type="submit">Create draft offering</april:button>
            </form>
        </slot:content>
    </april:card>
@endsection
