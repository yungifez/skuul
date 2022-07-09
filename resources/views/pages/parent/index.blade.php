@extends('adminlte::page')

@section('title', __('parents'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Parents') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('parents.index'), 'text'=> 'Parents', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-parents-table')
    
    @livewire('display-status')
@stop
