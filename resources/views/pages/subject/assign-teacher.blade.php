@extends('adminlte::page')

@section('title', __('Assign teachers to subjects'))


@section('content_header')
    <h1 class=" ">
        {{ __('Assign teachers to subjects') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('subjects.index'), 'text'=> 'subjects'],
        ['href'=> route('subjects.assign-teacher'), 'text'=> 'Assign teacher', 'active'],
    ]])

@stop

@section('content')
    @livewire('assign-teacher-to-subjects')
    
    @livewire('display-status')
@stop
