@extends('adminlte::page')

@section('title', __('Notices'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Notices') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('notices.index'), 'text'=> 'Notices', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-notices-table')
    
    @livewire('display-status')
@stop
