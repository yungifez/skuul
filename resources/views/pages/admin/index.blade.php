@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('admins.index'), 'text'=> 'Administrators', 'active'],
]])

@section('title',  __('Administrators'))

@section('page_heading',   __('Administrators'))

@section('content', )
    @livewire('list-admins-table')
@endsection