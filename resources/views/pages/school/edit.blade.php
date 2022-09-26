@extends('adminlte::page')

@section('title', __("Edit $school->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $school->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('schools.index'), 'text'=> 'Schools' , ],
        ['href'=> route('schools.edit', $school->id), 'text'=> 'Settings' , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-school-form', ['school' => $school])

@livewire('display-status')

@endsection
