@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('parents.index'), 'text'=> 'Parents'],
        ['href'=> route('parents.create'), 'text'=> 'Create parent', 'active'],
]])

@section('title',  __('Create parent'))

@section('page_heading',   __('Create parent'))

@section('content' )
    @livewire('create-parent-form')
@endsection