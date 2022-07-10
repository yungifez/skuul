@extends('adminlte::page')

@section('title', __('Create grade'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create grade') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('grade-systems.index'), 'text'=> 'grade-systems'],
        ['href'=> route('grade-systems.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-grade-system-form')

    @livewire('display-status')
@stop
