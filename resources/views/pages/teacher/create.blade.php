@extends('adminlte::page')

@section('title', __('Create teachers'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create teachers') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('teachers.index'), 'text'=> 'teachers'],
        ['href'=> route('teachers.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-teacher-form')

    @livewire('display-status')
@stop
