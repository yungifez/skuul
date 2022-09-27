@extends('adminlte::page')

@section('title', __('Promote Students'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Promote Students') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'Students'],
        ['href'=> route('students.promote'), 'text'=> 'Promote Students', 'active'],
    ]])

@stop

@section('content') 
    @livewire('promote-students')
    
    @livewire('display-status')
@stop
