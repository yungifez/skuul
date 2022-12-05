@extends('adminlte::page')

@section('title', __("$admin->name's profile"))

@section('content_header')
    <h1 class="">
        {{ __("$admin->name's profile") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('admins.index'), 'text'=> 'admins'],
        ['href'=> route('admins.show', $admin->id), 'text'=> "View $admin->name's profile", 'active'],
    ]])
@endsection

@section('content')

    @livewire('show-admin-profile', ['admin' => $admin])

    @livewire('display-status')

    
@endsection
