@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations', 'active'],
]])

@section('title', __('Organizations'))

@section('page_heading', __('Organizations'))

@section('content')
    <april:card>
        <slot:title>Organizations</slot:title>
        <slot:description>Organizations own campuses. Campus roles and operational records stay scoped to the working school.</slot:description>
        <slot:content class="space-y-4">
            @can('create', \App\Models\Organization::class)
                <div class="flex justify-end">
                    <a href="{{ route('organizations.create') }}"><april:button>Create organization</april:button></a>
                </div>
            @endcan
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($organizations as $organization)
                    <a href="{{ route('organizations.show', $organization) }}" class="rounded-lg border p-5 transition hover:bg-muted/50">
                        <p class="font-semibold">{{ $organization->name }}</p>
                        <p class="text-sm text-muted-foreground">{{ $organization->schools_count }} campuses</p>
                    </a>
                @empty
                    <p class="text-muted-foreground">No organizations are available to this account.</p>
                @endforelse
            </div>
        </slot:content>
    </april:card>
@endsection
