@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => 'Course offerings'],
    ['href' => route('course-offerings.edit', $courseOffering), 'text' => 'Edit roster', 'active'],
]])

@section('title', 'Edit roster')
@section('page_heading', 'Edit roster')

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Edit who attends {{ $courseOffering->subject->name }}</slot:title>
        <slot:description>{{ $courseOffering->academicYear->name }} · {{ $courseOffering->academicLevel->name }} · {{ $courseOffering->academicPeriod->display_name }}</slot:description>
        <slot:content>
            <form method="POST" action="{{ route('course-offerings.update', $courseOffering) }}" class="space-y-6" x-data="{ rosterMode: @js(old('roster_mode', $courseOffering->roster_mode->value)) }">
                @csrf
                @method('PUT')
                <x-display-validation-errors />

                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-1"><april:label for="roster-mode">Who attends</april:label><x-help-tooltip label="Roster help">Choose how this subject gets its learners. You can return here later as learners join or leave.</x-help-tooltip></div>
                    <select id="roster-mode" name="roster_mode" x-model="rosterMode" class="rounded-md border border-input bg-background px-3 py-2" required>
                        @foreach ($rosterModes as $rosterMode)
                            <option value="{{ $rosterMode->value }}">{{ school_roster_label($rosterMode) }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-cloak x-show="rosterMode === 'home_section' || rosterMode === 'combined_home_sections'" class="flex flex-col gap-2">
                    <div class="flex items-center gap-1"><april:label for="cycle-sections">Participating sections</april:label><x-help-tooltip label="Participating sections help">For one section, choose exactly one. For combined sections, choose two or more sections from this class.</x-help-tooltip></div>
                    <april:select id="cycle-sections" name="academic_cycle_section_ids[]" multiple placeholder="Select sections">
                        @foreach ($academicCycleSections as $academicCycleSection)
                            <option value="{{ $academicCycleSection->id }}" @selected(in_array($academicCycleSection->id, old('academic_cycle_section_ids', $courseOffering->cycleSections->modelKeys())))>
                                {{ $academicCycleSection->label ?? $academicCycleSection->name }}
                            </option>
                        @endforeach
                    </april:select>
                    <p x-show="rosterMode === 'home_section'" class="text-sm text-muted-foreground">Choose one section.</p>
                    <p x-show="rosterMode === 'combined_home_sections'" class="text-sm text-muted-foreground">Choose at least two sections to teach together.</p>
                </div>

                <div x-cloak x-show="rosterMode === 'academic_level'" class="rounded-md border border-primary/30 bg-primary/5 p-3 text-sm">
                    <p class="font-medium">{{ $courseOffering->academicLevel->is_group ? 'Whole group' : 'Whole academic level' }}</p>
                    <p class="mt-1 text-muted-foreground">{{ $courseOffering->academicLevel->is_group ? 'Every learner in the child classes under '.$courseOffering->academicLevel->name.' is included.' : 'Every learner in '.$courseOffering->academicLevel->name.' is included.' }} You do not need to choose sections or named learners.</p>
                </div>

                <div x-cloak x-show="rosterMode === 'individual_roster'" class="flex flex-col gap-2">
                    <div class="flex items-center gap-1"><april:label for="student-records">Named learners</april:label><x-help-tooltip label="Named learners help">Select the learners who take this subject. You can leave this empty while setting up the year and return later.</x-help-tooltip></div>
                    <april:select id="student-records" name="student_record_ids[]" multiple placeholder="Select learners">
                        @foreach ($studentRecords as $studentRecord)
                            <option value="{{ $studentRecord->id }}" @selected(in_array($studentRecord->id, old('student_record_ids', $courseOffering->studentRecords->modelKeys())))>{{ $studentRecord->user?->name ?? $studentRecord->admission_number }} · {{ $studentRecord->academicCycleSection?->label ?? $studentRecord->academicCycleSection?->name }}</option>
                        @endforeach
                    </april:select>
                </div>

                <div class="space-y-1">
                    <april:button type="submit">Save roster</april:button>
                    <p class="text-sm text-muted-foreground">You can update this roster again as learners are added or moved.</p>
                </div>
            </form>
        </slot:content>
    </april:card>
@endsection
