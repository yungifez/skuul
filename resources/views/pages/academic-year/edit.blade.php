@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('academic-years.index'), 'text' => school_terms('academic_year', 'School years') , ],
        ['href'=> route('academic-years.edit', $academicYear->id), 'text'=> "Edit {$academicYear->name}" , 'active']
]])
@section('title', __('Edit '.$academicYear->name.' '.strtolower(school_term('academic_year', 'school year'))))

@section('page_heading', __('Edit '.$academicYear->name.' '.strtolower(school_term('academic_year', 'school year'))))

@section('content')
<div class="grid gap-6 lg:grid-cols-2 lg:items-start">
    @livewire('academic-calendar-form', ['academicYear' => $academicYear])

    @can('viewInstructionalModel', $academicYear)
        <x-instructional-model-summary :academic-year="$academicYear" />
    @endcan
</div>
@endsection
