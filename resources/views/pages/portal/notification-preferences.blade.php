@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('portal.overview'), 'text' => 'My school'],
    ['text' => 'Notification settings', 'active'],
]])

@section('title', 'Notification settings')
@section('page_heading', 'Notification settings')

@section('content')
    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Choose your notice emails</h2>
            <p class="mt-1 text-sm text-muted-foreground">
                Decide which schools may email you a copy of the optional notices they publish. Your choices are separate
                for each campus.
            </p>
        </div>

        @if ($errors->has('preferences'))
            <april:alert variant="destructive">
                <slot:title>Your settings were not saved</slot:title>
                <slot:description>{{ $errors->first('preferences') }}</slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>School notice delivery</slot:title>
            <slot:description>Notices remain available in the family portal even when email is off.</slot:description>
            <slot:content>
                <form method="POST" action="{{ route('portal.notification-preferences.update') }}" class="flex flex-col gap-6">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col divide-y rounded-lg border">
                        @foreach ($schools as $school)
                            @php($preference = $preferences->get($school->id))
                            <label for="school-{{ $school->id }}" class="flex cursor-pointer items-start justify-between gap-4 p-4">
                                <span class="min-w-0">
                                    <span class="flex flex-wrap items-center gap-2">
                                        <span class="font-medium">{{ $school->name }}</span>
                                        @if ($preference?->email_enabled ?? true)
                                            <april:badge variant="secondary">On</april:badge>
                                        @else
                                            <april:badge variant="outline">Off</april:badge>
                                        @endif
                                    </span>
                                    <span class="mt-1 block text-sm text-muted-foreground">
                                        Email me optional notices from this school.
                                    </span>
                                </span>
                                <input type="hidden" name="preferences[{{ $school->id }}]" value="0">
                                <input type="checkbox" id="school-{{ $school->id }}" name="preferences[{{ $school->id }}]" value="1"
                                    class="mt-1 size-4 shrink-0 rounded border-input accent-primary"
                                    @checked($preference?->email_enabled ?? true)>
                            </label>
                        @endforeach
                    </div>

                    <april:button type="submit" class="w-fit">
                        <x-lucide-save class="mr-2 size-4" />
                        Save settings
                    </april:button>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What this does not change</slot:title>
            <slot:content>
                <ul class="flex flex-col gap-3 text-sm text-muted-foreground">
                    <li class="flex gap-3">
                        <x-lucide-bell class="mt-0.5 size-4 shrink-0" />
                        <span>Notices stay in the application. This choice only controls the optional email copy.</span>
                    </li>
                    <li class="flex gap-3">
                        <x-lucide-shield-check class="mt-0.5 size-4 shrink-0" />
                        <span>Account and safety messages, such as password resets, are always sent.</span>
                    </li>
                </ul>
            </slot:content>
        </april:card>
    </div>
@endsection
