@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('campus-moves.index'), 'text' => 'Campus moves', 'active'],
]])

@section('title', __('Campus moves'))

@section('page_heading', __('Campus moves'))

@section('content')
    <livewire:list-campus-move-requests />
@endsection
