@extends('adminlte::page')

@section('title', __('Create Class'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Create Class') }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('classes.index'), 'text'=> ' Classes' ,],
        ['href'=> route('classes.create'), 'text'=> 'Create' , 'active'],
    ]])
@endsection

@section('content')

    @livewire('create-class-form')

    @livewire('display-status')
@endsection
