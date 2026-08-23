@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('roles.index'), 'text' => 'Roles'],
    ['href' => route('roles.edit', $role->id), 'text' => $role->name, 'active'],
]])

@section('title', $role->name)

@section('page_heading', $role->name)

@section('content')
<div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">{{ $role->name }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">
                {{ $role->description ?? 'A named set of permissions at this campus.' }}
                @if ($role->isBuiltIn())
                    This role is built in: it can be given out, but not rewritten.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if (!$role->isBuiltIn())
                @if ($role->isArchived())
                    <form action="{{ route('roles.restore', $role->id) }}" method="POST">
                        @csrf
                        <april:button type="submit" variant="outline" size="sm">Offer it again</april:button>
                    </form>
                @else
                    <form action="{{ route('roles.archive', $role->id) }}" method="POST">
                        @csrf
                        <april:button type="submit" variant="ghost" size="sm">Stop offering it</april:button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <x-display-validation-errors />

    @if ($canWrite)
        <form action="{{ route('roles.update', $role->id) }}" method="POST" class="flex flex-col gap-6">
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 text-card-foreground shadow-sm">
                <div class="flex flex-col gap-2">
                    <label for="role-description" class="text-sm font-medium leading-none">What it is for</label>
                    <input id="role-description" name="description" maxlength="255"
                        value="{{ old('description', $role->description) }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
            </div>

            <x-role-permissions :grantable="$grantable" :held="old('permissions', $role->permissions->pluck('name')->all())" />

            <div>
                <april:button type="submit">Save the role</april:button>
            </div>
        </form>
    @else
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 text-sm text-muted-foreground shadow-sm">
            This role holds: {{ $role->permissions->pluck('name')->join(', ', ' and ') ?: 'nothing yet' }}.
        </div>
    @endif

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Who holds it</h3>
            <p class="text-sm text-muted-foreground">Only people who work at this campus can be given it.</p>
        </div>

        @unless ($role->isArchived())
            <form action="{{ route('roles.members.store', $role->id) }}" method="POST" class="flex flex-wrap items-end gap-3 border-b p-6">
                @csrf
                <div class="flex min-w-64 flex-1 flex-col gap-2">
                    <label for="role-member" class="text-sm font-medium leading-none">Person</label>
                    <select id="role-member" name="user_id" required
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} &middot; {{ $member->email }}</option>
                        @endforeach
                    </select>
                </div>
                <april:button type="submit">Give the role</april:button>
            </form>
        @endunless

        @if ($holders->isEmpty())
            <p class="p-6 text-sm text-muted-foreground">Nobody holds this role yet.</p>
        @else
            <div class="flex flex-col divide-y">
                @foreach ($holders as $holder)
                    <div class="flex items-center justify-between gap-3 p-4">
                        <div>
                            <p class="text-sm font-medium">{{ $holder->name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $holder->email }}</p>
                        </div>
                        <form action="{{ route('roles.members.destroy', $role->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="user_id" value="{{ $holder->id }}">
                            <april:button type="submit" variant="ghost" size="sm">Take it away</april:button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @can('create', \App\Models\CampusRole::class)
        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Start from this one</h3>
                <p class="text-sm text-muted-foreground">The copy holds only what you can hand out yourself.</p>
            </div>
            <form action="{{ route('roles.duplicate', $role->id) }}" method="POST" class="flex flex-wrap items-end gap-3 p-6">
                @csrf
                <div class="flex min-w-64 flex-1 flex-col gap-2">
                    <label for="copy-name" class="text-sm font-medium leading-none">Name of the copy</label>
                    <input id="copy-name" name="name" required maxlength="100"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
                <april:button type="submit" variant="outline">Copy it</april:button>
            </form>
        </div>
    @endcan
</div>
@endsection
