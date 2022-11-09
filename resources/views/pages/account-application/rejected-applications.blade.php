@extends('adminlte::page')

@section('title', __('Rejected Account Applications'))

@section('content_header')
    <h1 class=" font-weight-semibold">
        {{ __('Rejected Account Applications') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('account-applications.index'), 'text'=> 'Account Applications'],
        ['href'=> route('account-applications.rejected-applications'), 'text'=> 'Rejected Acount Applications', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-rejected-account-applications-table')
    
    @livewire('display-status')
@stop
