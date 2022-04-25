@extends('adminlte::page')

@section('title', __("Edit {$academicYear->name()}"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit {$academicYear->name()}") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('academic-years.index'), 'text' => 'academic-years' , ],
        ['href'=> route('academic-years.edit', $academicYear->id), 'text'=> "Edit {$academicYear->name()}" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-academic-year-form', ['academicYear' => $academicYear])

@livewire('display-status')

@endsection
