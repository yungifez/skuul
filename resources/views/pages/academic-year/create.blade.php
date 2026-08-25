@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> 'School calendars' ,],
    ['href'=> route('academic-years.create'), 'text'=> 'Set up' , 'active'],

]])

@section('title',  __('Set up school calendar'))

@section('page_heading',   __('Set up school calendar'))

@section('content' )
@livewire('academic-calendar-form')
@endsection
