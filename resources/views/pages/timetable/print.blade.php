@extends('layouts.print')

@section('title', 'Print Student Profile')

@section('content')
    @livewire('show-timetable', ['timetable' => $timetable])
@endsection

@section('style')
    <style>
        td, th {
            padding: 0.25rem;
        }
    </style>
@endSection