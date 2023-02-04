@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> 'Academic years' , 'active']
]])

@section('title', __('Academic years'))

@section('page_heading',  __('Academic years'))

@section('content', )
    @livewire('set-academic-year')

    @livewire('list-academic-years-table')
@endsection