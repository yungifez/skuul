@extends('adminlte::page')

@section('title', __('Dashboard'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Dashboard') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard', 'active'],
    ]])

@stop

@section('content')
    
    <div class="my-3">@livewire('school-set')</div>
    @livewire('dashboard-data-cards')
    
    @livewire('academic-year-set')

    @livewire('display-status')
@stop

