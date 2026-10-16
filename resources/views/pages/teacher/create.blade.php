@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('teachers.index'), 'text'=> 'Teachers'],
        ['href'=> route('teachers.create'), 'text'=> 'Create teacher', 'active'],
]])

@section('title',  __('Create teacher'))

@section('page_heading',   __('Create teacher'))

@section('content' )
    @livewire('create-teacher-form')
@endsection