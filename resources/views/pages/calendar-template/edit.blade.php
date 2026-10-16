@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name],
    ['href' => route('organizations.calendar-templates.index', $organization), 'text' => 'Calendar templates'],
    ['href' => route('organizations.calendar-templates.edit', [$organization, $calendarTemplate]), 'text' => $calendarTemplate->name, 'active'],
]])

@section('title', $calendarTemplate->name)
@section('page_heading', $calendarTemplate->name)

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>Template definition</slot:title>
            <slot:description>Changes shape future generated school years. Existing school years keep their own dated records.</slot:description>
            <slot:content><livewire:calendar-template-form :organization="$organization" :calendar-template="$calendarTemplate" /></slot:content>
        </april:card>

        <april:card>
            <slot:title>Campus calendars</slot:title>
            <slot:description>Draft a school year for a campus from this template, or choose which campuses follow it.</slot:description>
            <slot:content><livewire:calendar-template-campuses :organization="$organization" :calendar-template="$calendarTemplate" /></slot:content>
        </april:card>
    </div>
@endsection
