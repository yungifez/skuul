@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-cycle-sections.index'), 'text' => 'Cycle sections'],
    ['href' => route('academic-cycle-sections.show', $academicCycleSection), 'text' => $academicCycleSection->name],
    ['href' => route('academic-cycle-sections.edit', $academicCycleSection), 'text' => 'Edit', 'active'],
]])

@section('title', __("Edit $academicCycleSection->name"))
@section('page_heading', __("Edit $academicCycleSection->name"))

@section('content')
    <april:card class="mx-auto max-w-3xl">
        <slot:title>Edit {{ $academicCycleSection->academicLevel->name }} · {{ $academicCycleSection->name }} · {{ $academicCycleSection->academicYear->name }}</slot:title>
        <slot:description>
            A change here updates this one section in this one cycle. No learner, result, attendance, or timetable record moves.
        </slot:description>
        <slot:content>
            <x-academic-cycle-section-form
                :action="route('academic-cycle-sections.update', $academicCycleSection)"
                method="PUT"
                :section="$academicCycleSection"
                :teachers="$teachers"
                submit-label="Save changes"
                :cancel-href="route('academic-cycle-sections.show', $academicCycleSection)" />
        </slot:content>
    </april:card>
@endsection
