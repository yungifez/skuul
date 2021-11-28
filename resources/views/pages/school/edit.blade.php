@extends('adminlte::page')

@section('title', __("Edit $school->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $school->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('schools.settings'), 'text'=> 'School Settings' , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-school', ['school' => $school])

@livewire('display-status')

@endsection
