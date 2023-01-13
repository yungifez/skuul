@extends('layouts.print')

@section('title', 'Print exam tabulation')

@section('content')
    @livewire('mark-tabulation', ['tabulatedRecords' => $tabulatedRecords,'totalMarksAttainableInEachSubject' => $totalMarksAttainableInEachSubject, 'subjects' => $subjects])
@endsection

@section('style')
    <style>
        .hide-on-print {
            display:none;
        }
    </style>
@endSection