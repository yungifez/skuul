@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-levels.index'), 'text' => 'Academic levels'],
    ['href' => route('academic-levels.show', $academicLevel), 'text' => $academicLevel->name],
    ['href' => route('academic-levels.edit', $academicLevel), 'text' => 'Edit', 'active'],
]])

@section('title', __("Edit $academicLevel->name"))
@section('page_heading', __("Edit $academicLevel->name"))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Edit the reusable level</slot:title>
        <slot:description>
            A change here renames the level everywhere it is read. It never moves a learner, a result, or a section between cycles.
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
