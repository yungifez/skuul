@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('academic-years.index'), 'text' => 'academic-years' , ],
        ['href'=> route('academic-years.edit', $academicYear->id), 'text'=> "Edit {$academicYear->name}" , 'active']
]])
@section('title', __("Edit {$academicYear->name}"))

@section('page_heading', __("Edit {$academicYear->name}"))

@section('content')
<div class="grid gap-6 lg:grid-cols-2 lg:items-start">
    @livewire('edit-academic-year-form', ['academicYear' => $academicYear])

    @can('viewInstructionalModel', $academicYear)
        <x-instructional-model-summary :academic-year="$academicYear" />
    @endcan
</div>
@endsection
