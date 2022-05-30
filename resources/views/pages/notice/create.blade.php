@extends('adminlte::page')

@section('title', __('Create notices'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create notices') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('notices.index'), 'text'=> 'notices'],
        ['href'=> route('notices.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-notice-form')

    @livewire('display-status')
@stop
