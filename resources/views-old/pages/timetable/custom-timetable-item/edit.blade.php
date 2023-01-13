@extends('adminlte::page')

@section('title', __("Edit Custom Timetable Item ($customTimetableItem) "))


@section('content_header')
    <h1 class=""> 
        {{ __("Edit Custom Timetable Item ($customTimetableItem->name) ") }}
    </h1>

    @livewire('show-set-school')
    
    @livewire('breadcrumbs', ['paths' => [
        ['href'=> route('dashboard'), 'text'=> 'Dashboard'],
        ['href'=> route('timetables.index'), 'text'=> 'Timetables'],
        ['href'=> route('custom-timetable-items.index'), 'text'=> 'Custom timetable items'],
        ['href'=> route('custom-timetable-items.edit', $customTimetableItem->id), 'text'=> "Edit $customTimetableItem->name ", 'active'],
    ]])

@stop

@section('content') 
    @livewire('edit-custom-timetable-item-form', ['customTimetableItem'=> $customTimetableItem])

    @livewire('display-status')
@stop
