@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('roles.index'), 'text' => 'Roles', 'active'],
]])

@section('title', __('Roles'))

@section('page_heading', __('Roles'))

@section('content')
<div class="mx-auto flex w-full max-w-4xl flex-col gap-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">What each job is allowed to do</h2>
            <p class="mt-1 text-sm text-muted-foreground">
                A role is a named set of permissions and nothing more. Nothing in the application reads a role's name,
                so this campus can write Registrar or Finance Officer and mean exactly what it says. A role can only
                carry permissions you already hold here.
            </p>
        </div>
        @can('create', \App\Models\CampusRole::class)
            <april:button-link href="{{ route('roles.create') }}">Write a role</april:button-link>
        @endcan
    </div>

    <x-display-validation-errors />

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="grid gap-3 p-4 md:hidden">
            @foreach ($roles as $role)
                <article class="rounded-lg border bg-background p-4 shadow-sm {{ $role->isArchived() ? 'text-muted-foreground' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="break-words font-medium">{{ $role->name }}</h3>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @if ($role->isBuiltIn())
                                    <april:badge variant="secondary">Built in</april:badge>
                                @endif
                                @if ($role->isArchived())
                                    <april:badge variant="outline">Retired</april:badge>
                                @endif
                            </div>
                            @if ($role->description !== null)
                                <p class="mt-2 break-words text-xs text-muted-foreground">{{ $role->description }}</p>
                            @endif
                        </div>
                        @can('assign', $role)
                            <april:button-link href="{{ route('roles.edit', $role->id) }}" variant="outline" size="sm" aria-label="Open the {{ $role->name }} role">Open</april:button-link>
                        @endcan
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-4 border-t pt-4 text-sm">
                        <div>
                            <dt class="text-muted-foreground">Permissions</dt>
                            <dd class="mt-1 font-medium">{{ $role->permissions_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-muted-foreground">People</dt>
                            <dd class="mt-1 font-medium">{{ $role->users_count }}</dd>
                        </div>
                    </dl>
                </article>
            @endforeach
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                        <th class="p-4 font-medium">Role</th>
                        <th class="p-4 font-medium">Holds</th>
                        <th class="p-4 font-medium">People</th>
                        <th class="p-4 font-medium"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr class="border-b last:border-0 {{ $role->isArchived() ? 'text-muted-foreground' : '' }}">
                            <td class="p-4">
                                <span class="font-medium">{{ $role->name }}</span>
                                @if ($role->isBuiltIn())
                                    <april:badge variant="secondary" class="ml-2">Built in</april:badge>
                                @endif
                                @if ($role->isArchived())
                                    <april:badge variant="outline" class="ml-2">Retired</april:badge>
                                @endif
                                @if ($role->description !== null)
                                    <span class="block text-xs text-muted-foreground">{{ $role->description }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-muted-foreground">{{ $role->permissions_count }} permissions</td>
                            <td class="p-4 text-muted-foreground">{{ $role->users_count }}</td>
                            <td class="p-4 text-right">
                                @can('assign', $role)
                                    <april:button-link href="{{ route('roles.edit', $role->id) }}" variant="outline" size="sm" aria-label="Open the {{ $role->name }} role">Open</april:button-link>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
