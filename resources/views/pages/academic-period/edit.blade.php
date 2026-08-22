@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-periods.index'), 'text'=> school_terms('period', 'Academic periods') , ],
    ['href'=> route('academic-periods.edit', $academicPeriod->id), 'text'=> "Edit $academicPeriod->display_name" , 'active']
]])
@section('title', __("Edit $academicPeriod->display_name"))

@section('page_heading',  __("Edit $academicPeriod->display_name"))

@section('content')
    @livewire('edit-academic-period-form', ['academicPeriod' => $academicPeriod]
)@endsection
