@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.calendar-templates.index', $organization), 'text' => 'Calendar templates'],
    ['href' => route('organizations.calendar-templates.create', $organization), 'text' => 'Create', 'active'],
]])

@section('title', __('Create calendar template'))
@section('page_heading', __('Create calendar template'))

@section('content')
    <april:card>
        <slot:title>Calendar shape</slot:title>
        <slot:description>Use terms, semesters, trimesters, or your local labels. Dates are offsets from the first day of each future school year.</slot:description>
        <slot:content><x-calendar-template-form :organization="$organization" /></slot:content>
    </april:card>
@endsection
