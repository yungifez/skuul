@extends('adminlte::page')

@section('title', __('Schools'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Schools') }}
    </h1>

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('schools.create'), 'text'=> 'Create School' , 'active']
    ]])
@endsection

@section('content')
    @livewire('display-status')
    @livewire('create-school')
@endsection
