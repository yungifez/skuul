@extends('layouts.print')

@section('title', $timetable->name)

@section('content')
    <h1 class="school-name">{{auth()->user()->school->name}}</h1>
    @livewire('show-timetable', ['timetable' => $timetable])
@endsection

@section('style')
    <style>
        .school-name{
            font-size: 1.6rem;
        }
        h4{
            margin: 0;
        }
        td, th {
            padding: 0.05rem;
        }
        p{
            padding: 0.20rem;
        }
        @page{
            size: landscape;
        }
        header{
            display: none;
        }
    </style>
@endSection