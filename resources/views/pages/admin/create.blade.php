@extends('adminlte::page')

@section('title', __('Create admin'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Create admin') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('admins.index'), 'text'=> 'admins'],
        ['href'=> route('admins.create'), 'text'=> 'create', 'active'],
    ]])

@stop

@section('content') 
    @livewire('create-admin-form')

    @livewire('display-status')
@stop
