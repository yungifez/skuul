@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => school_terms('course', 'Course').' being taught'],
    ['href' => route('course-offerings.gradebook.show', $courseOffering), 'text' => 'Gradebook', 'active'],
]])

@section('title', 'Gradebook · '.$courseOffering->subject->name)
@section('page_heading', 'Gradebook')

@section('page_actions')
    <april:button-link href="{{ route('course-offerings.index') }}" variant="outline">Back to {{ school_terms('course', 'courses') }}</april:button-link>
@endsection

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>{{ $courseOffering->subject->name }} <span class="font-normal text-muted-foreground">· {{ $courseOffering->academicLevel->label ?? $courseOffering->academicLevel->name }}</span></slot:title>
            <slot:description>
                {{ $courseOffering->academicYear->name }} · {{ $courseOffering->academicPeriod->display_name }}
                · {{ $courseOffering->roster_mode->label() }}
                · {{ $students->count() }} learner{{ $students->count() === 1 ? '' : 's' }}
            </slot:description>
        </april:card>

        @if ($errors->has('gradebook'))
            <div class="rounded-lg border border-destructive/40 bg-destructive/10 px-4 py-3 text-sm text-destructive">{{ $errors->first('gradebook') }}</div>
        @endif

        @can('manageGradebook', $courseOffering)
            <april:card>
                <slot:title>Add an assessment</slot:title>
                <slot:description>Add assignments, tests, projects, observations, or exam papers without leaving this gradebook.</slot:description>
                <slot:content>
                    <form method="POST" action="{{ route('course-offerings.gradebook.items.store', $courseOffering) }}" class="grid gap-3 md:grid-cols-6">
                        @csrf
                        <div class="md:col-span-2">
                            <label for="assessment-name" class="mb-1 block text-sm font-medium">Assessment name</label>
                            <input id="assessment-name" name="name" value="{{ old('name') }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Term project">
                        </div>
                        <div>
                            <label for="assessment-type" class="mb-1 block text-sm font-medium">Type</label>
                            <select id="assessment-type" name="type" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                @foreach (\App\Enums\GradeItemType::cases() as $type)
                                    <option value="{{ $type->value }}" @selected(old('type', \App\Enums\GradeItemType::Numeric->value) === $type->value)>{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="assessment-points" class="mb-1 block text-sm font-medium">Maximum points</label>
                            <input id="assessment-points" name="max_points" type="number" min="0.01" step="0.01" value="{{ old('max_points') }}" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="100">
                        </div>
                        <div>
                            <label for="assessment-scale" class="mb-1 block text-sm font-medium">Grading scale</label>
                            <select id="assessment-scale" name="grading_scale_id" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="">Use only for a scale</option>
                                @foreach ($gradingScales as $gradingScale)
                                    <option value="{{ $gradingScale->id }}" @selected((string) old('grading_scale_id') === (string) $gradingScale->id)>{{ $gradingScale->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="assessment-weight" class="mb-1 block text-sm font-medium">Weight</label>
                            <input id="assessment-weight" name="weight" type="number" min="0.001" step="0.001" value="{{ old('weight', 1) }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                        </div>
                        <div class="flex items-end"><april:button type="submit" class="w-full">Add assessment</april:button></div>
                    </form>
                </slot:content>
            </april:card>
        @endcan

        <april:card>
            <slot:title>Record grades and publish results</slot:title>
            <slot:description>Saving a grade changes working marks only. Publish creates the official, append-only result that families and reports can read.</slot:description>
            <slot:content>
                @if ($gradeItems->isEmpty())
                    <div class="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">Add the first assessment to begin recording grades.</div>
                @elseif ($students->isEmpty())
                    <div class="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">No learners match this offering's roster. Update the course offering before entering grades.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[920px] text-sm">
                            <thead class="border-b text-left text-muted-foreground">
                                <tr>
                                    <th class="sticky left-0 z-10 bg-background px-3 py-2">Learner</th>
                                    @foreach ($gradeItems as $gradeItem)
                                        <th class="min-w-56 px-3 py-2">
                                            <span class="block font-medium text-foreground">{{ $gradeItem->name }}</span>
                                            <span class="text-xs">{{ $gradeItem->category?->name ? $gradeItem->category->name.' · ' : '' }}{{ $gradeItem->gradingScale?->name ?? ($gradeItem->max_points ? $gradeItem->max_points.' points' : $gradeItem->type->label()) }}</span>
                                        </th>
                                    @endforeach
                                    <th class="px-3 py-2">Official result</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($students as $student)
                                    <tr class="align-top">
                                        <th scope="row" class="sticky left-0 z-10 bg-background px-3 py-3 text-left font-medium">
                                            {{ $student->user?->name ?? $student->admission_number }}
                                            <span class="block text-xs font-normal text-muted-foreground">{{ $student->admission_number }}</span>
                                        </th>
                                        @foreach ($gradeItems as $gradeItem)
                                            @php($entry = $gradeItem->entries->firstWhere('student_record_id', $student->id))
                                            <td class="px-3 py-3">
                                                @can('manageGradebook', $courseOffering)
                                                    <form method="POST" action="{{ route('course-offerings.gradebook.entries.store', $courseOffering) }}" class="grid grid-cols-[1fr_auto] gap-2">
                                                        @csrf
                                                        <input type="hidden" name="grade_item_id" value="{{ $gradeItem->id }}">
                                                        <input type="hidden" name="student_record_id" value="{{ $student->id }}">
                                                        @if ($gradeItem->type === \App\Enums\GradeItemType::Numeric)
                                                            <input aria-label="{{ $gradeItem->name }} for {{ $student->user?->name ?? $student->admission_number }}" name="points" type="number" min="0" step="0.01" max="{{ $gradeItem->max_points }}" value="{{ $entry?->points }}" class="min-w-0 rounded-md border border-input bg-background px-2 py-1.5" placeholder="Mark">
                                                        @elseif ($gradeItem->type === \App\Enums\GradeItemType::Text)
                                                            <input aria-label="{{ $gradeItem->name }} comment for {{ $student->user?->name ?? $student->admission_number }}" name="comment" value="{{ $entry?->comment }}" class="min-w-0 rounded-md border border-input bg-background px-2 py-1.5" placeholder="Comment">
                                                        @else
                                                            <select aria-label="{{ $gradeItem->name }} grade for {{ $student->user?->name ?? $student->admission_number }}" name="grading_scale_option_id" class="min-w-0 rounded-md border border-input bg-background px-2 py-1.5">
                                                                <option value="">Choose grade</option>
                                                                @foreach ($gradeItem->gradingScale?->options ?? [] as $option)
                                                                    <option value="{{ $option->id }}" @selected($entry?->grading_scale_option_id === $option->id)>{{ $option->label }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                        <select aria-label="Grade state" name="state" class="rounded-md border border-input bg-background px-2 py-1.5">
                                                            @foreach (\App\Enums\GradeEntryState::cases() as $state)
                                                                <option value="{{ $state->value }}" @selected(($entry?->state ?? \App\Enums\GradeEntryState::Graded) === $state)>{{ $state->label() }}</option>
                                                            @endforeach
                                                        </select>
                                                        <april:button size="sm" type="submit" class="col-span-2">Save</april:button>
                                                    </form>
                                                @else
                                                    <span>{{ $entry?->gradingScaleOption?->label ?? $entry?->points ?? $entry?->comment ?? '—' }}</span>
                                                    @if ($entry !== null && $entry->state !== \App\Enums\GradeEntryState::Graded)
                                                        <span class="block text-xs text-muted-foreground">{{ $entry->state->label() }}</span>
                                                    @endif
                                                @endcan
                                        @endforeach
                                        @php($publishedResult = $publishedResults->get($student->id))
                                        <td class="px-3 py-3">
                                            @if ($publishedResult !== null)
                                                <span class="block font-medium">{{ $publishedResult->percentage === null ? 'No percentage' : number_format($publishedResult->percentage, 2).'%' }}</span>
                                                <span class="block text-xs text-muted-foreground">Revision {{ $publishedResult->revision }} · {{ $publishedResult->published_at->format('j M Y') }}</span>
                                            @else
                                                <span class="text-muted-foreground">Not published</span>
                                            @endif
                                            @can('publishResult', $courseOffering)
                                                <form method="POST" action="{{ route('course-offerings.gradebook.results.publish', $courseOffering) }}" class="mt-2">
                                                    @csrf
                                                    <input type="hidden" name="student_record_id" value="{{ $student->id }}">
                                                    <april:button size="sm" variant="outline" type="submit">{{ $publishedResult === null ? 'Publish' : 'Publish revision' }}</april:button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
