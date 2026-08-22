@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations'],
    ['href' => route('organizations.show', $organization), 'text' => $organization->name, 'active'],
]])

@section('title', $organization->name)

@section('page_heading', $organization->name)

@section('content')
    <april:card>
        <slot:title>{{ $organization->name }}</slot:title>
        <slot:description>{{ $organization->code }}</slot:description>
        <slot:content class="space-y-6">
            <div class="flex flex-wrap justify-end gap-2">
                @can('viewReports', $organization)
                    <a href="{{ route('organizations.dashboard', $organization) }}"><april:button variant="outline">Organization overview</april:button></a>
                @endcan
                @can('manageMembers', $organization)
                    <a href="{{ route('organizations.members.index', $organization) }}"><april:button variant="outline">Members</april:button></a>
                @endcan
                @can('manageCalendar', $organization)
                    <a href="{{ route('organizations.calendar-templates.index', $organization) }}"><april:button variant="outline">Calendar templates</april:button></a>
                @endcan
                @can('update', $organization)
                    <a href="{{ route('organizations.edit', $organization) }}"><april:button>Organization settings</april:button></a>
                @endcan
            </div>
            <div>
                <h2 class="font-semibold">Campuses</h2>
                <p class="mt-1 text-sm text-muted-foreground">An organization administrator can manage campus setup. Operational access still needs a school membership and school role.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($organization->schools as $school)
                    <a href="{{ route('schools.edit', $school) }}" class="rounded-lg border p-5 transition hover:bg-muted/50">
                        <p class="font-semibold">{{ $school->name }}</p>
                        <p class="text-sm text-muted-foreground">{{ $school->address }}</p>
                    </a>
                @empty
                    <p class="text-muted-foreground">No campuses have been added.</p>
                @endforelse
            </div>
            @can('create', \App\Models\School::class)
                <a href="{{ route('schools.create') }}"><april:button>Add campus</april:button></a>
            @endcan
        </slot:content>
    </april:card>
@endsection
