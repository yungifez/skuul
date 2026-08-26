@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-years.index'), 'text'=> school_terms('academic_year', 'School years') ,],
    ['href'=> route('academic-years.create'), 'text'=> 'Set up' , 'active'],

]])

@section('title', __('Set up '.strtolower(school_term('academic_year', 'school year'))))

@section('page_heading', __('Set up '.strtolower(school_term('academic_year', 'school year'))))

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
        <april:steps :items="[
            ['value' => 'calendar', 'label' => 'Dates and periods', 'description' => 'Set the calendar', 'state' => 'current'],
            ['value' => 'teaching', 'label' => 'Teaching approach', 'description' => 'Choose the grouping', 'state' => 'upcoming'],
            ['value' => 'structure', 'label' => 'Classes and teachers', 'description' => 'Build the year', 'state' => 'upcoming'],
            ['value' => 'subjects', 'label' => 'Subjects', 'description' => 'Choose what is taught', 'state' => 'upcoming'],
            ['value' => 'review', 'label' => 'Review and publish', 'description' => 'Make it available', 'state' => 'upcoming'],
        ]" current="calendar" />
        @livewire('academic-calendar-form', ['setupWizard' => true])
    </div>
@endsection
