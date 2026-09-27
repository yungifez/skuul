@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Requests', 'active'],
]])

@section('title', 'Requests')
@section('page_heading', 'Requests')

@section('content')
    <livewire:portal-requests :student-record="$studentRecord" />
@endsection
