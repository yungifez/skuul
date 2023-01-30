@extends('layouts.app', ['breadcrumbs' => [
     ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('admins.index'), 'text'=> 'Administrators'],
    ['href'=> route('admins.show', $admin->id), 'text'=> "View $admin->name's profile", 'active'],
]])

@section('title', __("$admin->name's profile"))

@section('page_heading', __("$admin->name's profile") )

@section('content')
    @livewire('show-admin-profile', ['admin' => $admin])
@endsection
