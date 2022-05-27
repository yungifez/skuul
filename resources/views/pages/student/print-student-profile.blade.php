@extends('layouts.print')

@section('title', 'Print Student Profile')

@section('content')
    @livewire('show-student-profile', ['student' => $student])
@endsection