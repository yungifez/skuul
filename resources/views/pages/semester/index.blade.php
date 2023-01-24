@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('semesters.index'), 'text'=> 'Semesters', 'active'],
]])

@section('title', __('Semesters'))

@section('page_heading',  __('Semesters'))

@section('content', ) 
    @livewire('set-semester')

    @livewire('list-semesters-table')
@endsection