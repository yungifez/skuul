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
            A level is reusable. Create an umbrella group such as “Kindergarten” first, then add specific levels such as “KG 1” and “KG 2” under it. Create a {{ strtolower(school_term('section', 'section')) }} inside each level for every {{ strtolower(school_term('academic_year', 'school year')) }}.
        </slot:description>
        <slot:content>
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
