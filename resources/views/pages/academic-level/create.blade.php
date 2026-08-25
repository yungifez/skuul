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
            A {{ strtolower(school_term('class_level', 'class')) }} is reusable. Create it once, then create a {{ strtolower(school_term('section', 'section')) }} inside it for each {{ strtolower(school_term('academic_year', 'school year')) }}.
        </slot:description>
        <slot:content>
            <x-academic-level-form
                :action="route('academic-levels.store')"
                :academic-levels="$academicLevels"
                submit-label="Create class"
                :cancel-href="route('academic-levels.index')" />
        </slot:content>
    </april:card>
@endsection
