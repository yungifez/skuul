@extends('adminlte::page')

@section('title', __('Create School'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Create School') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('schools.index'), 'text'=> 'Schools'],
        ['href'=> route('schools.create'), 'text'=> 'Create' , 'active'],
    ]])
@endsection

@section('content')
    @livewire('display-status')
    @livewire('create-school-form')
@endsection
