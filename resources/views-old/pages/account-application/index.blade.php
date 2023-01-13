@extends('adminlte::page')

@section('title', __('Account Applications'))


@section('content_header')
    <h1 class=" ">
        {{ __('Account Applications') }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('account-applications.index'), 'text'=> 'Account Applications', 'active'],
    ]])

@stop

@section('content') 
    @livewire('list-account-applications-table')
    
    @livewire('display-status')
@stop
