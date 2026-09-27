@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('calendar-events.index'), 'text' => 'Calendar'],
    ['text' => 'Add a day', 'active'],
]])

@section('title', 'Add a day to the calendar')
@section('page_heading', 'Add a day to the calendar')

@section('content')
    <livewire:calendar-event-editor :day="request()->string('day')->toString()" />
@endsection
