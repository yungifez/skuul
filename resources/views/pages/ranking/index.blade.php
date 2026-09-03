@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('rankings.index'), 'text' => 'Rankings', 'active'],
]])

@section('title', 'Rankings')
@section('page_heading', 'Rankings')

@section('content')
    <livewire:ranking-filters />
@endsection
