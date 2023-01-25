@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('notices.index'), 'text'=> 'notices'],
    ['href'=> route('notices.create'), 'text'=> 'create', 'active'],
]])

@section('title', __('Create notices'))

@section('page_heading',  __('Create notices'))

@section('content' )
    @livewire('create-notice-form')
@endsection