@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('exams.index'), 'text'=> 'Exams', 'active'],
]])

@section('title',  __('Exams'))

@section('page_heading',   __('Exams'))

@section('content', )
    @livewire('list-exams-table')
@endsection