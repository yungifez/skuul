@extends('adminlte::page')

@section('title', __('Classes'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Classes') }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('classes.index'), 'text'=> 'Classes'],
        ['href'=> route('classes.show', $class->id), 'text'=> "View $class->name", 'active'],
    ]])
@endsection

@section('content')
    @livewire('show-class', ['class' => $class])

    @livewire('display-status')
@endsection
