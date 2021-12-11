@extends('adminlte::page')

@section('title', __('Create class Section'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create Class Section') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('sections.index'), 'text'=> 'Sections'],
        ['href'=> route('sections.create'), 'text'=> 'Create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-section-form')
    
    @livewire('display-status')
@stop
