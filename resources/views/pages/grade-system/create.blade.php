@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('grade-systems.index'), 'text'=> 'grade-systems'],
    ['href'=> route('grade-systems.create'), 'text'=> 'create', 'active'],
]])

@section('title', __('Create Grade'))

@section('page_heading', __('Create Grade'))

@section('content')
    @livewire('create-grade-system-form')
@endsection
