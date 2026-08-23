@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('roles.index'), 'text' => 'Roles'],
    ['href' => route('roles.create'), 'text' => 'Write a role', 'active'],
]])

@section('title', __('Write a role'))

@section('page_heading', __('Write a role'))

@section('content')
<div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    <p class="text-sm text-muted-foreground">
        The list below is what you yourself can do at this campus. You cannot put anything else in a role, so a role
        can never hand out more authority than the person who wrote it has.
    </p>

    <x-display-validation-errors />

    <form action="{{ route('roles.store') }}" method="POST" class="flex flex-col gap-6">
        @csrf

        <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 text-card-foreground shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <label for="role-name" class="text-sm font-medium leading-none">Name</label>
                    <input id="role-name" name="name" required maxlength="100" value="{{ old('name') }}"
                        placeholder="Registrar"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
                <div class="flex flex-col gap-2">
                    <label for="role-description" class="text-sm font-medium leading-none">What it is for <span class="font-normal text-muted-foreground">(optional)</span></label>
                    <input id="role-description" name="description" maxlength="255" value="{{ old('description') }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
            </div>
        </div>

        <x-role-permissions :grantable="$grantable" :held="old('permissions', [])" />

        <div>
            <april:button type="submit">Write it</april:button>
        </div>
    </form>
</div>
@endsection
