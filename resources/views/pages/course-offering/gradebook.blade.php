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
            <slot:title>{{ $courseOffering->subject->name }} <span class="font-normal text-muted-foreground">· {{ $courseOffering->academicLevel->name }}</span></slot:title>
            <slot:description>
                {{ $courseOffering->academicYear->name }} · {{ $courseOffering->academicPeriod->display_name }}
                · {{ school_roster_label($courseOffering->roster_mode) }}
                · {{ $students->count() }} learner{{ $students->count() === 1 ? '' : 's' }}
            </slot:description>
        </april:card>

        <april:card class="border-primary/30 bg-primary/[0.03]">
            <slot:title>Gradebook workflow</slot:title>
            <slot:description>Set up the assessments once, enter working marks, then submit results for approval.</slot:description>
            <slot:content>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="flex gap-3">
                        <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground">1</span>
                        <div><p class="font-medium">Set up assessments</p><p class="text-sm text-muted-foreground">Choose categories, weights, and assessment types.</p></div>
                    </div>
                    <div class="flex gap-3">
                        <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground">2</span>
                        <div><p class="font-medium">Enter grades</p><p class="text-sm text-muted-foreground">Save working marks in the learner grid below.</p></div>
                    </div>
                    <div class="flex gap-3">
                        <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-foreground">3</span>
                        <div><p class="font-medium">Submit results</p><p class="text-sm text-muted-foreground">Send completed results for approval and publication.</p></div>
                    </div>
                </div>
            </slot:content>
        </april:card>

        @if ($errors->has('gradebook'))
            <div class="rounded-lg border border-destructive/40 bg-destructive/10 px-4 py-3 text-sm text-destructive">{{ $errors->first('gradebook') }}</div>
        @endif
        <x-display-validation-errors />

        @can('manageGradebook', $courseOffering)
            <details id="assessment-setup" class="rounded-xl border bg-card p-5" @if ($gradeItems->isEmpty()) open @endif>
                <summary class="flex cursor-pointer list-none items-start justify-between gap-4">
                    <span>
                        <span class="block text-lg font-semibold">Assessment setup</span>
                        <span class="mt-1 block text-sm text-muted-foreground">Add or adjust the work that appears in the grade entry grid.</span>
                    </span>
                    <span class="flex shrink-0 flex-wrap justify-end gap-2 text-xs">
                        <span class="rounded-full border px-2.5 py-1">{{ $gradeCategories->count() }} categor{{ $gradeCategories->count() === 1 ? 'y' : 'ies' }}</span>
                        <span class="rounded-full border px-2.5 py-1">{{ $gradeItems->count() }} assessment{{ $gradeItems->count() === 1 ? '' : 's' }}</span>
                    </span>
                </summary>
                <div class="mt-5 space-y-6">
            @if ($gradeItems->isEmpty() && $courseOffering->gradeCategories->isEmpty() && $assessmentTemplates->isNotEmpty())
                <april:card>
                    <slot:title>Start from a school template</slot:title>
                    <slot:description>Copy a proven assessment structure into this empty gradebook, then add any subject-specific assessments before entering learner grades.</slot:description>
                    <slot:content>
                        <form method="POST" action="{{ route('course-offerings.gradebook.templates.apply', $courseOffering) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            @csrf
                            <div class="min-w-0 flex-1"><label for="assessment-template" class="mb-1 block text-sm font-medium">Template</label><select id="assessment-template" name="assessment_template_id" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">@foreach ($assessmentTemplates as $assessmentTemplate)<option value="{{ $assessmentTemplate->id }}" @selected((string) old('assessment_template_id') === (string) $assessmentTemplate->id)>{{ $assessmentTemplate->name }} · {{ $assessmentTemplate->categories_count }} categories, {{ $assessmentTemplate->items_count }} assessments</option>@endforeach</select>@error('assessment_template_id')<p class="mt-1 text-sm text-destructive">{{ $message }}</p>@enderror</div>
                            <april:button type="submit">Apply template</april:button>
                        </form>
                    </slot:content>
                </april:card>
            @endif

            <april:card>
                <slot:title>Assessment categories</slot:title>
                <slot:description>Group assessments such as classwork, projects, and exams, then choose how each group contributes to the result.</slot:description>
                <slot:content>
                    @if ($gradeCategories->isNotEmpty())
                        <div class="mb-4 flex flex-wrap gap-2">
                            @foreach ($gradeCategories as $gradeCategory)
                                <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs">
                                    <span class="font-medium">{{ $gradeCategory->name }}</span>
                                    <span class="text-muted-foreground">{{ $gradeCategory->aggregation->label() }} · {{ $gradeCategory->weight }}×</span>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('course-offerings.gradebook.categories.store', $courseOffering) }}" class="grid gap-3 sm:grid-cols-[1.4fr_1fr_0.7fr_auto] sm:items-end">
                        @csrf
                        <div>
                            <label for="category-name" class="mb-1 block text-sm font-medium">Category name</label>
                            <input id="category-name" name="name" value="{{ old('name') }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Classwork">
                        </div>
                        <div>
                            <label for="category-aggregation" class="mb-1 block text-sm font-medium">Calculation</label>
                            <select id="category-aggregation" name="aggregation" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                @foreach (\App\Enums\GradeAggregation::cases() as $aggregation)
                                    <option value="{{ $aggregation->value }}" @selected(old('aggregation', \App\Enums\GradeAggregation::WeightedMean->value) === $aggregation->value)>{{ $aggregation->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="category-weight" class="mb-1 block text-sm font-medium">Weight</label>
                            <input id="category-weight" name="weight" type="number" min="0.001" step="0.001" value="{{ old('weight', 1) }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                        </div>
                        <april:button type="submit">Add category</april:button>
                    </form>
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>Add an assessment</slot:title>
                <slot:description>Add assignments, tests, projects, observations, or any other work you grade by hand.</slot:description>
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
                            <input id="assessment-points" name="max_points" type="number" min="0.01" step="0.01" value="{{ old('max_points') }}" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Maximum points">
                        </div>
                        <div>
                            <label for="assessment-scale" class="mb-1 block text-sm font-medium">Grading scale</label>
                            <select id="assessment-scale" name="grading_scale_id" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="">Use only for a scale</option>
                                @foreach ($gradingScales as $gradingScale)
                                    <option value="{{ $gradingScale->id }}" @selected((string) old('grading_scale_id') === (string) $gradingScale->id)>{{ $gradingScale->name }} · {{ $gradingScale->scale_type->label() }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-muted-foreground">Percentage and GPA scales use their own maximum. Custom-point scales use this assessment’s maximum points.</p>
                        </div>
                        <div>
                            <label for="assessment-weight" class="mb-1 block text-sm font-medium">Weight</label>
                            <input id="assessment-weight" name="weight" type="number" min="0.001" step="0.001" value="{{ old('weight', 1) }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label for="assessment-category" class="mb-1 block text-sm font-medium">Category</label>
                            <select id="assessment-category" name="grade_category_id" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="">No category</option>
                                @foreach ($gradeCategories as $gradeCategory)
                                    <option value="{{ $gradeCategory->id }}" @selected((string) old('grade_category_id') === (string) $gradeCategory->id)>{{ $gradeCategory->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="assessment-due-on" class="mb-1 block text-sm font-medium">Due date <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <input id="assessment-due-on" name="due_on" type="date" value="{{ old('due_on') }}" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                        </div>
                        <div class="flex items-end"><april:button type="submit" class="w-full">Add assessment</april:button></div>
                    </form>
                </slot:content>
            </april:card>

            @if ($gradeItems->isNotEmpty())
                <april:card>
                    <slot:title>Assessment structure</slot:title>
                    <slot:description>Adjust names, grouping, weights, and dates. Learner marks stay unchanged.</slot:description>
                    <slot:content>
                        <div class="flex flex-col gap-3">
                            @foreach ($gradeItems as $gradeItem)
                                <div class="rounded-lg border p-4">
                                    <form method="POST" action="{{ route('course-offerings.gradebook.items.update', [$courseOffering, $gradeItem]) }}" class="grid gap-3 md:grid-cols-6 md:items-end">
                                        @csrf
                                        @method('PUT')
                                        <div class="md:col-span-2">
                                            <label for="item-name-{{ $gradeItem->id }}" class="mb-1 block text-xs font-medium">Assessment</label>
                                            <input id="item-name-{{ $gradeItem->id }}" name="name" value="{{ $gradeItem->name }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label for="item-category-{{ $gradeItem->id }}" class="mb-1 block text-xs font-medium">Category</label>
                                            <select id="item-category-{{ $gradeItem->id }}" name="grade_category_id" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                                <option value="">No category</option>
                                                @foreach ($gradeCategories as $gradeCategory)
                                                    <option value="{{ $gradeCategory->id }}" @selected($gradeItem->grade_category_id === $gradeCategory->id)>{{ $gradeCategory->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="item-points-{{ $gradeItem->id }}" class="mb-1 block text-xs font-medium">Maximum points</label>
                                            <input id="item-points-{{ $gradeItem->id }}" name="max_points" type="number" min="0.01" step="0.01" value="{{ $gradeItem->max_points }}" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label for="item-weight-{{ $gradeItem->id }}" class="mb-1 block text-xs font-medium">Weight</label>
                                            <input id="item-weight-{{ $gradeItem->id }}" name="weight" type="number" min="0.001" step="0.001" value="{{ $gradeItem->weight }}" required class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label for="item-due-on-{{ $gradeItem->id }}" class="mb-1 block text-xs font-medium">Due date</label>
                                            <input id="item-due-on-{{ $gradeItem->id }}" name="due_on" type="date" value="{{ $gradeItem->due_on?->format('Y-m-d') }}" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                        </div>
                                        <div class="flex gap-2 md:col-span-6">
                                            <april:button type="submit" size="sm">Save changes</april:button>
                                    </form>
                                    <form method="POST" action="{{ route('course-offerings.gradebook.items.destroy', [$courseOffering, $gradeItem]) }}" data-confirm="Delete this assessment? Learner marks must be removed first.">
                                        @csrf
                                        @method('DELETE')
                                        <april:button type="submit" variant="ghost" size="sm" class="text-destructive">Delete</april:button>
                                    </form>
                                        </div>
                                </div>
                            @endforeach
                        </div>
                    </slot:content>
                </april:card>
            @endif

            @if ($gradeItems->isNotEmpty() || $courseOffering->gradeCategories->isNotEmpty())
                <april:card>
                    <slot:title>Reuse this assessment structure</slot:title>
                    <slot:description>Save the categories and assessments you have configured as a school template. It carries no learner grades, due dates, or published results.</slot:description>
                    <slot:content>
                        <form method="POST" action="{{ route('course-offerings.gradebook.templates.store', $courseOffering) }}" class="grid gap-3 md:grid-cols-[1fr_2fr_auto]">
                            @csrf
                            <div><input name="template_name" value="{{ old('template_name') }}" required class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="Template name">@error('template_name')<p class="mt-1 text-sm text-destructive">{{ $message }}</p>@enderror</div>
                            <div><input name="description" value="{{ old('description') }}" class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" placeholder="When should staff use this template?">@error('description')<p class="mt-1 text-sm text-destructive">{{ $message }}</p>@enderror</div>
                            <april:button type="submit">Save as template</april:button>
                        </form>
                    </slot:content>
                </april:card>
            @endif
                </div>
            </details>
        @endcan

        <april:card>
            <slot:title>Record grades and publish results</slot:title>
            <slot:description>Enter working marks for each learner. Submit a result when it is ready for approval and publication.</slot:description>
            <slot:content>
                @if ($gradeItems->isEmpty())
                    <div class="rounded-lg border border-dashed p-8 text-center">
                        <p class="font-medium">No assessments have been added yet.</p>
                        <p class="mt-1 text-sm text-muted-foreground">Open Assessment setup above to add the first assessment before entering grades.</p>
                    </div>
                @elseif ($students->isEmpty())
                    <div class="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">No learners match this offering. Update who attends before entering grades.</div>
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
                                            @if ($gradeItem->due_on !== null)
                                                <span class="block text-xs text-muted-foreground">Due {{ $gradeItem->due_on->format('M j, Y') }}</span>
                                            @endif
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
                                        @php($submittedResult = $submittedResults->get($student->id))
                                        <td class="px-3 py-3">
                                            @if ($publishedResult !== null)
                                                <span class="block font-medium">{{ $publishedResult->percentage === null ? 'No percentage' : number_format($publishedResult->percentage, 2).'%' }}</span>
                                                <span class="block text-xs text-muted-foreground">Revision {{ $publishedResult->revision }} · {{ $publishedResult->published_at->format('j M Y') }}</span>
                                            @else
                                                <span class="text-muted-foreground">Not published</span>
                                            @endif
                                            @if ($submittedResult !== null && $submittedResult->approval_status !== \App\Enums\ResultApprovalStatus::Approved)
                                                <span class="mt-2 block text-xs font-medium {{ $submittedResult->approval_status === \App\Enums\ResultApprovalStatus::Rejected ? 'text-destructive' : 'text-amber-600 dark:text-amber-400' }}">
                                                    Revision {{ $submittedResult->revision }} · {{ $submittedResult->approval_status->label() }}
                                                </span>
                                            @endif
                                            @can('publishResult', $courseOffering)
                                                <form method="POST" action="{{ route('course-offerings.gradebook.results.publish', $courseOffering) }}" class="mt-2">
                                                    @csrf
                                                    <input type="hidden" name="student_record_id" value="{{ $student->id }}">
                                                    <april:button size="sm" variant="outline" type="submit">{{ $publishedResult === null ? 'Submit for approval' : 'Submit revision' }}</april:button>
                                                </form>
                                            @endcan
                                            @if ($submittedResult?->approval_status === \App\Enums\ResultApprovalStatus::Pending)
                                                @can('approveResult', $courseOffering)
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        <form method="POST" action="{{ route('course-offerings.gradebook.results.approve', $courseOffering) }}">
                                                            @csrf
                                                            <input type="hidden" name="result_snapshot_id" value="{{ $submittedResult->id }}">
                                                            <april:button size="sm" type="submit">Approve</april:button>
                                                        </form>
                                                        <form method="POST" action="{{ route('course-offerings.gradebook.results.reject', $courseOffering) }}" class="flex gap-2">
                                                            @csrf
                                                            <input type="hidden" name="result_snapshot_id" value="{{ $submittedResult->id }}">
                                                            <input name="reason" required maxlength="500" placeholder="Reason to reject" class="h-8 w-40 rounded-md border border-input bg-background px-2 text-xs">
                                                            <april:button size="sm" variant="outline" type="submit">Reject</april:button>
                                                        </form>
                                                    </div>
                                                @endcan
                                            @endif
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
