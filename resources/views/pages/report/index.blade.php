@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('reports.index'), 'text' => 'Reports', 'active'],
]])

@section('title', __('Reports'))

@section('page_heading', __('Reports'))

@section('content')
    @livewire('report-desk')
@endsection
