@extends('adminlte::page')

@section('title', __('Admins'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Admins') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('admins.index'), 'text'=> 'admins', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-admins-table')
    
    @livewire('display-status')
@stop
