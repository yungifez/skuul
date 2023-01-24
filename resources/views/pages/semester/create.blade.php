@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('semesters.index'), 'text'=> 'Semesters'],
    ['href'=> route('semesters.create'), 'text'=> 'Create' , 'active'],
]])

@section('title', __('Create semester'))

@section('page_heading',  __('Create semester'))

@section('content' )
    @livewire('create-semester-form')
@endsection