@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('academic-periods.index'), 'text'=> 'AcademicPeriods'],
    ['href'=> route('academic-periods.create'), 'text'=> 'Create' , 'active'],
]])

@section('title', __('Create academic period'))

@section('page_heading',  __('Create academic period'))

@section('content' )
    @livewire('create-academic-period-form')
@endsection