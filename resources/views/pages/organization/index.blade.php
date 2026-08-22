@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('organizations.index'), 'text' => 'Organizations', 'active'],
]])

@section('title', __('Organizations'))

@section('page_heading', __('Organizations'))

@section('page_actions')
    <x-resource-create-action :href="route('organizations.create')" ability="create" :arguments="[\App\Models\Organization::class]">Add organization</x-resource-create-action>
@endsection

@section('content')
    <april:card>
        <slot:title>Organizations</slot:title>
        <slot:description>Organizations own campuses. Campus roles and operational records stay scoped to the working school.</slot:description>
        <slot:content class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($organizations as $organization)
                    <a href="{{ route('organizations.show', $organization) }}" class="rounded-lg border p-5 transition hover:bg-muted/50">
                        <p class="font-semibold">{{ $organization->name }}</p>
                        <p class="text-sm text-muted-foreground">{{ $organization->schools_count }} campuses</p>
                    </a>
                @empty
                    <div class="flex flex-col items-start gap-3 text-muted-foreground">
                        <p>No organizations are available to this account.</p>
                        <x-resource-create-action :href="route('organizations.create')" ability="create" :arguments="[\App\Models\Organization::class]">Add organization</x-resource-create-action>
                    </div>
                @endforelse
            </div>
        </slot:content>
    </april:card>
@endsection
