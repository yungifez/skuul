@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('course-offerings.index'), 'text' => school_terms('course', 'Course').' being taught'],
    ['href' => route('course-offerings.bulk-create', ['academic_year_id' => $selectedAcademicYear->id]), 'text' => 'Subject setup', 'active'],
]])

@section('title', 'Subject setup')
@section('page_heading', 'Subject setup')

@section('page_actions')
    <div class="flex flex-wrap gap-2">
        <april:button-link href="{{ route('course-offerings.index', ['academic_year_id' => $selectedAcademicYear->id]) }}" variant="outline">Manage all offerings</april:button-link>
        @can('create', \App\Models\CourseOffering::class)
            <april:button-link href="{{ route('course-offerings.bulk-create.form', ['academic_year_id' => $selectedAcademicYear->id, 'setup' => request()->boolean('setup') ? 1 : null]) }}">Set up across levels</april:button-link>
            <april:button-link href="{{ route('course-offerings.create', ['academic_year_id' => $selectedAcademicYear->id, 'setup' => request()->boolean('setup') ? 1 : null]) }}" variant="outline">Add one offering</april:button-link>
        @endcan
        @if (request()->boolean('setup'))
            <april:button-link href="{{ route('academic-years.show', $selectedAcademicYear) }}" variant="ghost">Save and finish later</april:button-link>
        @endif
    </div>
@endsection

@section('content')
    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
        <april:card>
            <slot:title>Subjects for {{ $selectedAcademicYear->name }}</slot:title>
            <slot:description>Review every subject in the school catalogue and see the classes, sections, and reporting periods where it is planned this year.</slot:description>
            <slot:content>
                @livewire('academic-year-subject-assignments-table', ['academicYear' => $selectedAcademicYear])
            </slot:content>
        </april:card>
    </div>
@endsection
