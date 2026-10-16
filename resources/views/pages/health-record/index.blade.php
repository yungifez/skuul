@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('health-records.index'), 'text' => 'Health records', 'active'],
]])

@section('title', 'Health records')
@section('page_heading', 'Health records')

@section('content')
    @livewire('student-health-record-directory')
@endsection
