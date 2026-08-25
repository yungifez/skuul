@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-levels.index'), 'text' => school_terms('class_level', 'Classes')],
    ['href' => route('academic-levels.show', $academicLevel), 'text' => $academicLevel->name],
    ['href' => route('academic-levels.edit', $academicLevel), 'text' => 'Edit', 'active'],
]])

@section('title', __("Edit $academicLevel->name"))
@section('page_heading', __("Edit $academicLevel->name"))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Edit the reusable {{ strtolower(school_term('class_level', 'class')) }}</slot:title>
        <slot:description>
            A change here renames the {{ strtolower(school_term('class_level', 'class')) }} everywhere it is read. It never moves a learner, a result, or a {{ strtolower(school_term('section', 'section')) }} between school years.
        </slot:description>
        <slot:content>
            <x-academic-level-form
                :action="route('academic-levels.update', $academicLevel)"
                method="PUT"
                :academic-level="$academicLevel"
                :academic-levels="$academicLevels"
                submit-label="Save changes"
                :cancel-href="route('academic-levels.show', $academicLevel)" />
        </slot:content>
    </april:card>
@endsection
