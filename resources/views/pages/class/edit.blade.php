@extends('adminlte::page')

@section('title', __('Classes'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Classes') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('classes.index'), 'text'=> 'Classes' ],
        ['href'=> route('classes.edit', $myClass->id), 'text'=> 'Edit' ]
    ]])
@endsection

@section('content')

    @livewire('edit-class-form', ['myClass' => $myClass])

    @livewire('display-status')
@endsection
