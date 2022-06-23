@extends('adminlte::page')

@section('title', __('Create parent'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create parent') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('parents.index'), 'text'=> 'parents'],
        ['href'=> route('parents.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-parent-form')

    @livewire('display-status')
@stop
