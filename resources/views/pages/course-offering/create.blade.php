@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => 'Course offerings'],
    ['href' => route('course-offerings.create'), 'text' => 'Add subject', 'active'],
]])

@section('title', __('Add a subject to this year'))
@section('page_heading', __('Add a subject to this year'))

@section('content')
    @livewire('create-course-offering', ['academicYearId' => request()->integer('academic_year_id') ?: null, 'setup' => request()->boolean('setup')])
@endsection
