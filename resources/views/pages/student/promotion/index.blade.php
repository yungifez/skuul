@php
    $currentAcademicYear = current_school()->load('academicYear')->academicYear;
@endphp

@extends('layouts.app', ['breadcrumbs' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('students.index'), 'text'=> 'Students'],
        ['href'=> route('students.promotions'), 'text'=> "Promotions for $currentAcademicYear->name", 'active'],

]])

@section('title', __('Manage Promotions'))

@section('page_heading',  __('Manage Promotions'))

@section('page_actions')
    <x-resource-create-action :href="route('students.promote')" ability="promote" :arguments="[\App\Models\Promotion::class]">Promote learners</x-resource-create-action>
@endsection

@section('content', )

    @livewire('list-promotions-table',['academicYear'=> $currentAcademicYear->id])
@endsection