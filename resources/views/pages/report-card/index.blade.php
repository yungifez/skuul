@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('report-cards.index'), 'text' => 'Report cards', 'active'],
]])

@section('title', 'Report cards')
@section('page_heading', 'Report cards')

@section('content')
    <livewire:report-card-directory />
@endsection
