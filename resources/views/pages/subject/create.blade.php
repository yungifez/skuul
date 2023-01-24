@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('subjects.index'), 'text'=> 'subjects'],
    ['href'=> route('subjects.create'), 'text'=> 'Create' , 'active'],
]])

@section('title', __('Create subject'))

@section('page_heading',  __('Create subject'))

@section('content' )
    @livewire('create-subject-form')
@endsection