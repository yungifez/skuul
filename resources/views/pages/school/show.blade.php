@extends('adminlte::page')

@section('title', __("View $school->name details"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("View $school->name details") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('schools.index'), 'text'=> 'schools'],
        ['href'=> route('schools.show', $school->id), 'text'=> "View $school->name details", 'active'],
    ]])
@endsection

@section('content')
    @livewire('show-school', ['school' => $school])

    @livewire('display-status')

    
@endsection
