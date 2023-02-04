@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('subjects.index'), 'text'=> 'subjects', 'active'],
]])

@section('title', __('Subjects'))

@section('page_heading',  __('Subjects'))

@section('content', )
    @livewire('list-subjects-table')
@endsection