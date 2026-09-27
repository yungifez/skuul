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
    @php
        $periodStatus = $courseOffering->academicPeriod?->status;
        $gradebookAcceptsWrites = $periodStatus?->acceptsWrites() ?? false;
        $gradebookAcceptsNewWork = $periodStatus?->acceptsNewWork() ?? false;
    @endphp

    <div class="space-y-6">
        <april:card>
            <slot:title class="flex flex-wrap items-center justify-between gap-3">
                <span>{{ $courseOffering->subject->name }} <span class="font-normal text-muted-foreground">· {{ $courseOffering->academicLevel->name }}</span></span>
                <span class="flex flex-wrap justify-end gap-2 text-xs">
                    <span class="rounded-full border px-2.5 py-1">{{ $gradeItems->count() }} assessment{{ $gradeItems->count() === 1 ? '' : 's' }}</span>
                    <span class="rounded-full border px-2.5 py-1">{{ $publishedResults->count() }} published</span>
                    <span class="rounded-full border px-2.5 py-1 {{ $gradebookAcceptsNewWork ? 'border-primary/30 bg-primary text-primary-foreground' : ($gradebookAcceptsWrites ? 'border-blue-500/30 bg-blue-500/10 text-blue-700 dark:text-blue-300' : 'border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-300') }}">
                        {{ $gradebookAcceptsNewWork ? 'Editing open' : ($gradebookAcceptsWrites ? 'Corrections open' : 'Read-only') }}
                    </span>
                </span>
            </slot:title>
            <slot:description>
                {{ $courseOffering->academicYear->name }} · {{ $courseOffering->academicPeriod->display_name }}
                · {{ school_roster_label($courseOffering->roster_mode) }}
                · {{ $students->count() }} learner{{ $students->count() === 1 ? '' : 's' }}
            </slot:description>
        </april:card>

        @if ($errors->has('gradebook'))
            <div class="rounded-lg border border-destructive/40 bg-destructive/10 px-4 py-3 text-sm text-destructive">{{ $errors->first('gradebook') }}</div>
        @endif
        <x-display-validation-errors />

        @if (!$gradebookAcceptsWrites)
            <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm">
                <p class="font-medium text-amber-900 dark:text-amber-100">This gradebook is read-only.</p>
                <p class="mt-1 text-amber-800 dark:text-amber-200">The {{ $courseOffering->academicPeriod->display_name }} period is {{ strtolower($courseOffering->academicPeriod->status->label()) }}. Existing marks and published results remain available, but assessment setup and mark entry are locked.</p>
            </div>
        @endif

        @can('manageGradebook', $courseOffering)
            @if ($gradebookAcceptsNewWork)
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
                            <div class="min-w-0 flex-1"><april:label for="assessment-template">Template</april:label><april:native-select id="assessment-template" name="assessment_template_id">@foreach ($assessmentTemplates as $assessmentTemplate)<option value="{{ $assessmentTemplate->id }}" @selected((string) old('assessment_template_id') === (string) $assessmentTemplate->id)>{{ $assessmentTemplate->name }} · {{ $assessmentTemplate->categories_count }} categories, {{ $assessmentTemplate->items_count }} assessments</option>@endforeach</april:native-select><x-field-error name="assessment_template_id" class="mt-1" /></div>
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

                    <form method="POST" action="{{ route('course-offerings.gradebook.categories.store', $courseOffering) }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-[minmax(0,1.5fr)_minmax(12rem,1fr)_8rem_auto] xl:items-end">
                        @csrf
                        <div>
                            <april:label for="category-name">Category name</april:label>
                            <april:input id="category-name" name="name" value="{{ old('name') }}" required placeholder="Classwork" />
                        </div>
                        <div>
                            <april:label for="category-aggregation">Calculation</april:label>
                            <april:native-select id="category-aggregation" name="aggregation" required>
                                @foreach (\App\Enums\GradeAggregation::cases() as $aggregation)
                                    <option value="{{ $aggregation->value }}" @selected(old('aggregation', \App\Enums\GradeAggregation::WeightedMean->value) === $aggregation->value)>{{ $aggregation->label() }}</option>
                                @endforeach
                            </april:native-select>
                        </div>
                        <div>
                            <april:label for="category-weight">Weight</april:label>
                            <april:input id="category-weight" name="weight" type="number" min="0.001" step="0.001" value="{{ old('weight', 1) }}" required />
                        </div>
                        <april:button type="submit">Add category</april:button>
                    </form>
                </slot:content>
            </april:card>

            <april:card>
                <slot:title>Add an assessment</slot:title>
                <slot:description>Add assignments, tests, projects, observations, or any other work you grade by hand.</slot:description>
                <slot:content>
                    <form method="POST" action="{{ route('course-offerings.gradebook.items.store', $courseOffering) }}" class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-10 2xl:items-end">
                        @csrf
                        <div class="2xl:col-span-2">
                            <april:label for="assessment-name">Assessment name</april:label>
                            <april:input id="assessment-name" name="name" value="{{ old('name') }}" required placeholder="Term project" />
                        </div>
                        <div>
                            <april:label for="assessment-type">Type</april:label>
                            <april:native-select id="assessment-type" name="type">
                                @foreach (\App\Enums\GradeItemType::cases() as $type)
                                    <option value="{{ $type->value }}" @selected(old('type', \App\Enums\GradeItemType::Numeric->value) === $type->value)>{{ $type->label() }}</option>
                                @endforeach
                            </april:native-select>
                        </div>
                        <div>
                            <april:label for="assessment-points">Maximum points</april:label>
                            <april:input id="assessment-points" name="max_points" type="number" min="0.01" step="0.01" value="{{ old('max_points') }}" placeholder="Maximum points" />
                        </div>
                        <div class="2xl:col-span-2">
                            <april:label for="assessment-scale">Grading scale</april:label>
                            <april:native-select id="assessment-scale" name="grading_scale_id">
                                <option value="">Use only for a scale</option>
                                @foreach ($gradingScales as $gradingScale)
                                    <option value="{{ $gradingScale->id }}" @selected((string) old('grading_scale_id') === (string) $gradingScale->id)>{{ $gradingScale->name }} · {{ $gradingScale->scale_type->label() }}</option>
                                @endforeach
                            </april:native-select>
                            <p class="mt-1 text-xs text-muted-foreground">Percentage and GPA scales use their own maximum. Custom-point scales use this assessment’s maximum points.</p>
                        </div>
                        <div>
                            <april:label for="assessment-weight">Weight</april:label>
                            <april:input id="assessment-weight" name="weight" type="number" min="0.001" step="0.001" value="{{ old('weight', 1) }}" required />
                        </div>
                        <div>
                            <april:label for="assessment-category">Category</april:label>
                            <april:native-select id="assessment-category" name="grade_category_id">
                                <option value="">No category</option>
                                @foreach ($gradeCategories as $gradeCategory)
                                    <option value="{{ $gradeCategory->id }}" @selected((string) old('grade_category_id') === (string) $gradeCategory->id)>{{ $gradeCategory->name }}</option>
                                @endforeach
                            </april:native-select>
                        </div>
                        <div>
                            <april:label for="assessment-due-on">Due date <span class="font-normal text-muted-foreground">(optional)</span></april:label>
                            <april:input id="assessment-due-on" name="due_on" type="date" value="{{ old('due_on') }}" />
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
                                            <april:label for="item-name-{{ $gradeItem->id }}" class="text-xs">Assessment</april:label>
                                            <april:input id="item-name-{{ $gradeItem->id }}" name="name" value="{{ $gradeItem->name }}" required />
                                        </div>
                                        <div>
                                            <april:label for="item-category-{{ $gradeItem->id }}" class="text-xs">Category</april:label>
                                            <april:native-select id="item-category-{{ $gradeItem->id }}" name="grade_category_id">
                                                <option value="">No category</option>
                                                @foreach ($gradeCategories as $gradeCategory)
                                                    <option value="{{ $gradeCategory->id }}" @selected($gradeItem->grade_category_id === $gradeCategory->id)>{{ $gradeCategory->name }}</option>
                                                @endforeach
                                            </april:native-select>
                                        </div>
                                        <div>
                                            <april:label for="item-points-{{ $gradeItem->id }}" class="text-xs">Maximum points</april:label>
                                            <april:input id="item-points-{{ $gradeItem->id }}" name="max_points" type="number" min="0.01" step="0.01" value="{{ $gradeItem->max_points }}" />
                                        </div>
                                        <div>
                                            <april:label for="item-weight-{{ $gradeItem->id }}" class="text-xs">Weight</april:label>
                                            <april:input id="item-weight-{{ $gradeItem->id }}" name="weight" type="number" min="0.001" step="0.001" value="{{ $gradeItem->weight }}" required />
                                        </div>
                                        <div>
                                            <april:label for="item-due-on-{{ $gradeItem->id }}" class="text-xs">Due date</april:label>
                                            <april:input id="item-due-on-{{ $gradeItem->id }}" name="due_on" type="date" value="{{ $gradeItem->due_on?->format('Y-m-d') }}" />
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
                            <div><april:input name="template_name" value="{{ old('template_name') }}" required placeholder="Template name" /><x-field-error name="template_name" class="mt-1" /></div>
                            <div><april:input name="description" value="{{ old('description') }}" placeholder="When should staff use this template?" /><x-field-error name="description" class="mt-1" /></div>
                            <april:button type="submit">Save as template</april:button>
                        </form>
                    </slot:content>
                </april:card>
            @endif
                </div>
            </details>
            @endif
        @endcan

        <livewire:gradebook-mark-sheet :course-offering="$courseOffering" />
    </div>
@endsection
