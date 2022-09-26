@extends('adminlte::page')

@section('title', __("view {$academicYear->name()}"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("View {$academicYear->name()}") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('academic-years.index'), 'text'=> 'academic years'],
        ['href'=> route('academic-years.show', $academicYear->id), 'text'=> "View {$academicYear->name()}", 'active'],
    ]])
@endsection

@section('content')
    @livewire('show-academic-year', ['academicYear' => $academicYear])

    @livewire('display-status')

@endsection
