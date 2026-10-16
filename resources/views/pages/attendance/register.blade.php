@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Attendance register', 'active'],
]])

@section('title', 'Attendance register')
@section('page_heading', 'Attendance register')

@section('content')
    <livewire:attendance-register />
@endsection
