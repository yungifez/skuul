@extends('adminlte::page')

@section('title', __('Assign students to parent'))


@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Assign students to parent') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('parents.index'), 'text'=> 'Parents'],
        ['href'=> route('parents.assign-students',$parent->id), 'text'=> "Assign students to $parent->name", 'active'],
    ]])

@stop

@section('content') 
    @livewire('assign-students-to-parent',['parent' => $parent])
    
    @livewire('display-status')
@stop
