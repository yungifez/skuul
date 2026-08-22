@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['text' => 'Notice delivery', 'active'],
]])

@section('title', 'Notice delivery')
@section('page_heading', 'Notice delivery')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        @if (session('success'))
            <april:alert>
                <slot:title>Saved</slot:title>
                <slot:description>{{ session('success') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>Optional notice email</slot:title>
            <slot:description>Choose whether this school also emails you the notices it posts.</slot:description>
            <slot:content>
                <form method="POST" action="{{ route('notice-preferences.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <label for="email-enabled" class="flex cursor-pointer items-start justify-between gap-4 rounded-lg border p-4">
                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="font-medium">Email me optional notices from this school.</span>
                                @if ($preference->email_enabled)
                                    <april:badge variant="secondary">On</april:badge>
                                @else
                                    <april:badge variant="outline">Off</april:badge>
                                @endif
                            </span>
                            <span class="mt-1 block text-sm text-muted-foreground">
                                Turn this off to read notices only in the application.
                            </span>
                        </span>
                        <input type="hidden" name="email_enabled" value="0">
                        <input type="checkbox" id="email-enabled" name="email_enabled" value="1"
                            class="mt-1 size-4 shrink-0 rounded border-input accent-primary"
                            {{ $preference->email_enabled ? 'checked' : '' }}>
                    </label>

                    <april:button type="submit" class="w-fit">
                        <x-lucide-save class="mr-2 size-4" />
                        Save preference
                    </april:button>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What this does not change</slot:title>
            <slot:content>
                <ul class="space-y-3 text-sm text-muted-foreground">
                    <li class="flex gap-3">
                        <x-lucide-bell class="mt-0.5 size-4 shrink-0" />
                        <span>Notices stay in the application. This choice only stops the copy that arrives by email.</span>
                    </li>
                    <li class="flex gap-3">
                        <x-lucide-shield-check class="mt-0.5 size-4 shrink-0" />
                        <span>Account and safety messages, such as a password reset, are always sent. You cannot turn those off here.</span>
                    </li>
                    <li class="flex gap-3">
                        <x-lucide-building class="mt-0.5 size-4 shrink-0" />
                        <span>This choice covers {{ current_school()?->name ?? 'this school' }} only. Each school you belong to has its own setting.</span>
                    </li>
                </ul>
            </slot:content>
        </april:card>
    </div>
@endsection
