@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('schools.settings'), 'text' => 'School setup'],
    ['href' => route('schools.features.edit'), 'text' => 'School features', 'active'],
]])

@section('title', 'School features')
@section('page_heading', 'Choose the tools your school uses')

@php
    $groups = \App\Enums\Feature::grouped();
    $enabledCount = count(array_filter($features));
    $totalCount = count($features);
@endphp

@section('content')
    <form method="POST" action="{{ route('schools.features.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <april:card>
            <slot:title class="flex items-center gap-1">
                <span>School tools</span>
                <x-help-tooltip label="School tools help">Turn a tool off to hide it from daily work. Nothing is deleted, and permitted reports can still read the records.</x-help-tooltip>
            </slot:title>
            <slot:description>Turn optional tools on or off for this school.</slot:description>
            <slot:content>
                <p class="text-sm text-muted-foreground">
                    {{ $enabledCount }} of {{ $totalCount }} tools are on. Sign-in, permissions, the audit trail, and enrollment history are always on, so they are not listed here.
                </p>
            </slot:content>
        </april:card>

        @foreach ($groups as $group => $groupFeatures)
            <april:card>
                <slot:title>{{ $group }}</slot:title>
                <slot:content>
                    <div class="divide-y">
                        @foreach ($groupFeatures as $feature)
                            <label for="feature-{{ $feature->value }}"
                                class="flex cursor-pointer items-start justify-between gap-4 py-4 first:pt-0 last:pb-0">
                                <span class="min-w-0">
                                    <span class="flex flex-wrap items-center gap-2">
                                        <span class="font-medium">{{ $feature->label() }}</span>
                                        @if ($features[$feature->value])
                                            <april:badge variant="secondary">On</april:badge>
                                        @else
                                            <april:badge variant="outline">Off</april:badge>
                                        @endif
                                    </span>
                                    <span class="mt-1 block text-sm text-muted-foreground">{{ $feature->description() }}</span>
                                </span>
                                <input type="hidden" name="features[{{ $feature->value }}]" value="0">
                                <input type="checkbox" id="feature-{{ $feature->value }}"
                                    name="features[{{ $feature->value }}]" value="1"
                                    class="mt-1 size-4 shrink-0 rounded border-input accent-primary"
                                    {{ $features[$feature->value] ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>
                </slot:content>
            </april:card>
        @endforeach

        <div class="flex flex-wrap items-start gap-3">
            <april:button type="submit">
                <x-lucide-save class="mr-2 size-4" />
                Save feature choices
            </april:button>
            <p class="pt-2 text-sm text-muted-foreground">Changes apply to everybody in this school straight away.</p>
        </div>
    </form>
@endsection
