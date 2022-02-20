@extends('adminlte::page')

@section('title', __('Subjects'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Subjects') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('subjects.index'), 'text'=> 'subjects', 'active'],
    ]])

@stop

@section('content')
    @livewire('list-subjects-table')
    
    @livewire('display-status')
@stop
