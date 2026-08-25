@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> school_terms('academic_year', 'School years')],
    ['href'=> route('academic-years.show', $academicYear->id), 'text'=> $academicYear->name, 'active'],
]])

@section('title', __($academicYear->name.' '.strtolower(school_term('academic_year', 'school year'))))

@section('page_heading', __($academicYear->name.' '.strtolower(school_term('academic_year', 'school year'))))

@section('content')
    @livewire('show-academic-year', ['academicYear' => $academicYear])
@endsection
