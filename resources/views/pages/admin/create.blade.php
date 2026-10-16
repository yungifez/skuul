@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('admins.index'), 'text'=> 'Administrators'],
        ['href'=> route('admins.create'), 'text'=> 'Create administrator', 'active'],
]])

@section('title',  __('Create administrator'))

@section('page_heading',   __('Create administrator'))

@section('content' )
    @livewire('create-admin-form')
@endsection