@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('staff-leave.index'), 'text' => 'Staff leave', 'active'],
]])

@section('title', 'Staff leave')
@section('page_heading', 'Staff leave')

@section('content')
    <livewire:staff-leave-board />
@endsection
