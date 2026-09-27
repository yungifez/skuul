@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('roles.index'), 'text' => 'Roles', 'active'],
]])

@section('title', __('Roles'))

@section('page_heading', __('Roles'))

@section('page_actions')
    @can('create', \App\Models\CampusRole::class)
        <april:button-link href="{{ route('roles.create') }}" class="h-11 select-none">
            <x-lucide-plus class="mr-2 size-4" />
            Write a role
        </april:button-link>
    @endcan
@endsection

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-4">
    <p class="text-sm text-muted-foreground">
        A role is a named set of permissions. A role can only carry permissions you already hold here.
    </p>

    <ul class="divide-y border-y">
        @foreach ($roles as $role)
            <li class="flex items-center gap-3 py-3 {{ $role->isArchived() ? 'text-muted-foreground' : '' }}">
                <div class="min-w-0 flex-1">
                    <p class="flex flex-wrap items-center gap-2 text-sm font-medium">
                        @can('assign', $role)
                            <a href="{{ route('roles.edit', $role->id) }}" class="break-words hover:underline">{{ $role->name }}</a>
                        @else
                            <span class="break-words">{{ $role->name }}</span>
                        @endcan
                        @if ($role->isBuiltIn())
                            <april:badge variant="secondary">Built in</april:badge>
                        @endif
                        @if ($role->isArchived())
                            <april:badge variant="outline">No longer offered</april:badge>
                        @endif
                    </p>
                    <p class="break-words text-xs text-muted-foreground">
                        {{ $role->permissions_count }} {{ str('permission')->plural($role->permissions_count) }} · held by {{ $role->users_count }} here
                        @if ($role->description !== null)
                            · {{ $role->description }}
                        @endif
                    </p>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection
