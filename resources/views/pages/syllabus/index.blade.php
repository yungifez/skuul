@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('syllabi.index'), 'text'=> 'Syllabi', 'active'],
]])

@section('title',  __('Syllabi'))

@section('page_heading',  __('Syllabi'))

@section('content', )
    @livewire('list-syllabi-table')
@endsection