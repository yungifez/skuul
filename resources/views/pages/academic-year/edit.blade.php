@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('academic-years.index'), 'text' => 'academic-years' , ],
        ['href'=> route('academic-years.edit', $academicYear->id), 'text'=> "Edit {$academicYear->name}" , 'active']
]])
@section('title', __("Edit {$academicYear->name}"))

@section('page_heading', __("Edit {$academicYear->name}"))

@section('content')
@livewire('edit-academic-year-form', ['academicYear' => $academicYear])
@endsection
