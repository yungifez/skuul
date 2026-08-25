@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> 'School calendars'],
    ['href'=> route('academic-years.show', $academicYear->id), 'text'=> $academicYear->name, 'active'],
]])

@section('title', __("{$academicYear->name} school calendar"))

@section('page_heading', __("{$academicYear->name} school calendar") )

@section('content')
    @livewire('show-academic-year', ['academicYear' => $academicYear])
@endsection
