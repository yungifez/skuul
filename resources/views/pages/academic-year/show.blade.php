@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> 'academic years'],
    ['href'=> route('academic-years.show', $academicYear->id), 'text'=> "View {$academicYear->name}", 'active'],
]])

@section('title', __("View {$academicYear->name}"))

@section('page_heading', __("View {$academicYear->name}") )

@section('content')
    @livewire('show-academic-year', ['academicYear' => $academicYear])
@endsection
