@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-periods.index'), 'text'=> 'Academic periods' , ],
    ['href'=> route('academic-periods.edit', $academicPeriod->id), 'text'=> "Edit $academicPeriod->name" , 'active']
]])
@section('title', __("Edit $academicPeriod->name"))

@section('page_heading',  __("Edit $academicPeriod->name"))

@section('content')
    @livewire('edit-academic-period-form', ['academicPeriod' => $academicPeriod]
)@endsection
