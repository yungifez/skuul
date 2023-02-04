@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> ' Academic years' ,],
    ['href'=> route('academic-years.create'), 'text'=> 'Create' , 'active'],

]])

@section('title',  __('Create academic year'))

@section('page_heading',   __('Create academic year'))

@section('content' )
@livewire('create-academic-year-form')
@endsection