@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-levels.index'), 'text' => 'Academic levels'],
    ['href' => route('academic-levels.create'), 'text' => 'Add', 'active'],
]])

@section('title', __('Add academic level'))
@section('page_heading', __('Add academic level'))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Add a level this school teaches</slot:title>
        <slot:description>
            A level is reusable. Create it once, then create a section inside it for each academic cycle.
        </slot:description>
        <slot:content>
            <x-academic-level-form
                :action="route('academic-levels.store')"
                :academic-levels="$academicLevels"
                submit-label="Create academic level"
                :cancel-href="route('academic-levels.index')" />
        </slot:content>
    </april:card>
@endsection
