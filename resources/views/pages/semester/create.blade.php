@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('schools.index'), 'text'=> 'Schools'],
    ['href'=> route('schools.create'), 'text'=> 'Create' , 'active'],
]])

@section('title', __('Create school'))

@section('page_heading',  __('Create school'))

@section('content' )
    @livewire('create-school-form')
@endsection