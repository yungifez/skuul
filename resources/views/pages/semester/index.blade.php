@extends('adminlte::page')

@section('title', __('Semesters'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Semesters') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('semesters.index'), 'text'=> 'Semesters', 'active'],
    ]])

@stop

@section('content')
    @livewire('list-semesters-table')
    
    @livewire('display-status')
@stop
