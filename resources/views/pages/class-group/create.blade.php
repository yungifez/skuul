@extends('adminlte::page')

@section('title', __('Create Class Group'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Create Class Group') }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('class-groups.index'), 'text'=> 'Class Groups' ],
        ['href'=> route('class-groups.create'), 'text'=> 'Create', 'active'],
    ]])
@endsection

@section('content')

    @livewire('create-class-group-form')

    @livewire('display-status')
@endsection
