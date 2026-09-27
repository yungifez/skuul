@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => school_terms('course', 'Course').' being taught'],
    ['href' => route('course-offerings.bulk-create', ['academic_year_id' => $selectedAcademicYear->id]), 'text' => 'Subject setup'],
    ['text' => 'Set up across levels', 'active'],
]])

@section('title', 'Set up across levels')
@section('page_heading', 'Set up across levels')

@section('content')
    <livewire:set-up-subject-across-levels :academic-year="$selectedAcademicYear" :subject-id="request()->integer('subject_id') ?: null" :setup="request()->boolean('setup')" />
@endsection
