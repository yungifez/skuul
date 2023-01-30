@extends('layouts.app', ['breadcrumbs' => [
    ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
    ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
    ['href'=> route('custom-timetable-items.index'), 'text'=> 'Custom timetable items'],
    ['href'=> route('custom-timetable-items.edit', $customTimetableItem->id), 'text'=> "Edit $customTimetableItem->name ", 'active'],
]])
@section('title', __("Edit Custom Timetable Item ($customTimetableItem->name) "))

@section('page_heading', __("Edit Custom Timetable Item ($customTimetableItem->name) "))

@section('content')
    @livewire('edit-custom-timetable-item-form', ['customTimetableItem'=> $customTimetableItem])
@endsection
