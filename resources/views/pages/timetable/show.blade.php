@extends('adminlte::page')

@section('title', __("View $timetable->name"))

@section('content_header')
    <h1 class="font-weight-semibold">
        {{ __("View $timetable->name") }}
    </h1>

    @livewire('show-set-school')

    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'timetables'],
        ['href'=> route('timetables.show', $timetable->id), 'text'=> "View $timetable->name", 'active'],
    ]])
@endsection

@section('content')
    <a href="{{route('timetables.print',$timetable->id)}}" class="btn btn-primary my-3">Print Timetable</a>
    @livewire('show-timetable', ['timetable' => $timetable])

    @livewire('display-status')

@endsection
