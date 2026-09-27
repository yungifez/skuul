@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-years.index'), 'text' => 'Academic years'],
    ['href' => route('academic-years.edit', $academicYear->id), 'text' => $academicYear->name],
    ['href' => route('academic-years.instructional-model.edit', $academicYear->id), 'text' => 'Teaching setup', 'active'],
]])

@section('title', __("Teaching setup for {$academicYear->name}"))

@section('page_heading', __('Teaching setup'))

@section('content')
    @livewire('manage-instructional-model', ['academicYear' => $academicYear, 'setup' => request()->boolean('setup')])
@endsection
