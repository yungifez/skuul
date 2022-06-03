@extends('adminlte::page')

@section('title', __("Edit $admin->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("Edit $admin->name") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('admins.index'), 'text'=> 'admins' , ],
        ['href'=> route('admins.edit', $admin->id), 'text'=> "Edit $admin->name" , 'active']
    ]])
@endsection

@section('content')

@livewire('edit-admin-form', ['admin' => $admin])

@livewire('display-status')

@endsection
