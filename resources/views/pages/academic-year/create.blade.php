@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> school_terms('academic_year', 'School years') ,],
    ['href'=> route('academic-years.create'), 'text'=> 'Set up' , 'active'],

]])

@section('title', __('Set up '.strtolower(school_term('academic_year', 'school year'))))

@section('page_heading', __('Set up '.strtolower(school_term('academic_year', 'school year'))))

@section('content' )
@livewire('academic-calendar-form')
@endsection
