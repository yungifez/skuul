@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => school_terms('section', 'Sections')],
    ['href' => route('academic-cycle-sections.show', $academicCycleSection), 'text' => $academicCycleSection->name],
    ['href' => route('academic-cycle-sections.edit', $academicCycleSection), 'text' => 'Edit', 'active'],
]])

@section('title', __("Edit $academicCycleSection->name"))
@section('page_heading', __("Edit $academicCycleSection->name"))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Edit {{ $academicCycleSection->academicLevel->name }} · {{ $academicCycleSection->name }} · {{ $academicCycleSection->academicYear->name }}</slot:title>
        <slot:description>
            A change here updates this one {{ strtolower(school_term('section', 'section')) }} in this one {{ strtolower(school_term('academic_year', 'school year')) }}. No learner, result, attendance, or timetable record moves.
        </slot:description>
        <slot:content>
            <livewire:academic-cycle-section-form :academic-cycle-section="$academicCycleSection" />
        </slot:content>
    </april:card>
@endsection
