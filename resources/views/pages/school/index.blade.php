@extends('adminlte::page')

@section('title', __('Manage Schools'))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __('Manage Schools') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('schools.index'), 'text'=> 'Manage Schools' , 'active']
    ]])
@endsection

@section('content')

    @livewire('school-set')
    
    @livewire('list-schools-table')

    @livewire('display-status')
@endsection
