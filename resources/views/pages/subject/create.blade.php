@extends('adminlte::page')

@section('title', __('Create subjects'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create subjects') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('subjects.index'), 'text'=> 'subjects'],
        ['href'=> route('subjects.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-subject-form')

    @livewire('display-status')
@stop
