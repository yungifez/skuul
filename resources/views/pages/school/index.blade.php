@extends('adminlte::page')

@section('title', __('Schools'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Schools') }}
    </h1>

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('schools.index'), 'text'=> 'Schools' , 'active']
    ]])
@endsection

@section('content')

    <p>List of all schools</p>
    
    @livewire('list-schools-table')
@endsection
