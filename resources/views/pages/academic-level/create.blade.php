@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-levels.index'), 'text' => school_terms('class_level', 'Classes')],
    ['href' => route('academic-levels.create'), 'text' => 'Add', 'active'],
]])

@section('title', __('Add '.strtolower(school_term('class_level', 'class'))))
@section('page_heading', __('Add '.strtolower(school_term('class_level', 'class'))))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Add a {{ strtolower(school_term('class_level', 'class')) }} this school teaches</slot:title>
        <slot:description>
            Add one reusable level or level group. You will add this year’s {{ strtolower(school_term('section', 'section')) }} later.
        </slot:description>
        <slot:content>
            <div class="mb-6 flex items-start gap-3 rounded-lg border border-blue-500/30 bg-blue-500/10 p-4 text-sm text-blue-950 dark:text-blue-100">
                <x-lucide-git-branch class="mt-0.5 size-5 shrink-0 text-blue-700 dark:text-blue-300" />
                <div class="space-y-2">
                    <p class="font-semibold">How to structure levels</p>
                    <ol class="list-decimal space-y-1 pl-4">
                        <li>Create the umbrella group <strong>Kindergarten</strong>. Leave <strong>Level group</strong> blank.</li>
                        <li>Create the learner level <strong>KG 1</strong>. Choose <strong>Kindergarten</strong> as its level group. Repeat for <strong>KG 2</strong>.</li>
                        <li>In this year’s setup, add a section such as <strong>Blue</strong> under <strong>KG 1</strong>.</li>
                    </ol>
                    <p>If your school does not use groups, create a level such as <strong>Primary 4</strong> and leave <strong>Level group</strong> blank.</p>
                </div>
            </div>
            @if ($preselectedParent)
                <div class="mb-5 rounded-md border bg-muted/30 p-3 text-sm">
                    Adding a level under <span class="font-semibold">{{ $preselectedParent->name }}</span>. You can change the level group below.
                </div>
            @endif
            <x-academic-level-form
                :action="route('academic-levels.store', request()->boolean('setup') ? array_filter(['setup' => 1, 'academic_year_id' => request('academic_year_id')]) : [])"
                :academic-levels="$academicLevels"
                :preselected-parent-id="$preselectedParent?->id"
                submit-label="Create class"
                :cancel-href="route('academic-levels.index')" />
        </slot:content>
    </april:card>
@endsection
