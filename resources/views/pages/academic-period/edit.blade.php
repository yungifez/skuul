@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('academic-years.show', $academicPeriod->academic_year_id), 'text' => $academicPeriod->academicYear->name],
    ['href' => route('academic-periods.edit', $academicPeriod), 'text' => 'Edit', 'active'],
]])

@section('title', __('Edit '.$academicPeriod->displayName))
@section('page_heading', __('Edit '.$academicPeriod->displayName))

@section('content')
    @livewire('edit-academic-period-form', ['academicPeriod' => $academicPeriod])
@endsection
