@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('admins.index'), 'text'=> 'admins' , ],
        ['href'=> route('admins.edit', $admin->id), 'text'=> "Edit $admin->name" , 'active']
]])
@section('title',  __("Edit $admin->name"))

@section('page_heading',  __("Edit $admin->name"))

@section('content')
    @livewire('edit-admin-form', ['admin' => $admin])
@endsection
