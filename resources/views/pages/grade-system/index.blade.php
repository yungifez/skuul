@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('grade-systems.index'), 'text'=> 'Grade System', 'active'],
]])

@section('title', __('Grade systems'))

@section('page_heading',  __('Grade systems'))

@section('content', )
    @livewire('list-grade-systems-table')
@endsection