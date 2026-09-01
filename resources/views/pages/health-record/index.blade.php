@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('health-records.index'), 'text' => 'Health records', 'active'],
]])

@section('title', 'Health records')
@section('page_heading', 'Health records')

@php
    $missingCount = $learnerCount - $recordedCount;
@endphp

@section('content')
    <div class="space-y-6">
        <april:card>
            <slot:title>What the school must know in an emergency</slot:title>
            <slot:description>
                A health record is kept apart from the student profile on purpose. Reading a profile is ordinary
                work; reading this is not, and every change is written to the audit log.
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Learners</dt>
                            <dd class="text-2xl font-semibold">{{ $learnerCount }}</dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">With a record</dt>
                            <dd class="text-2xl font-semibold">{{ $recordedCount }}</dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Without one</dt>
                            <dd class="text-2xl font-semibold">{{ $missingCount }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">The school holds nothing for these children</p>
                        </div>
                    </dl>

                    <form method="GET" action="{{ route('health-records.index') }}" class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                        <div class="flex flex-col gap-2 lg:col-span-2">
                            <april:label for="search">Find a learner</april:label>
                            <april:input id="search" name="search" value="{{ $search }}" placeholder="Name or admission number" />
                        </div>

                        <label class="flex items-center gap-2 text-sm">
                            <input type="hidden" name="missing" value="0">
                            <input type="checkbox" name="missing" value="1" @checked($missingOnly)
                                class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                            Only the learners without a record
                        </label>

                        <div class="flex gap-2">
                            <april:button type="submit">
                                <x-lucide-search class="mr-2 size-4" />
                                Search
                            </april:button>
                            @if ($search !== null || $missingOnly)
                                <april:button-link href="{{ route('health-records.index') }}" variant="outline">Clear</april:button-link>
                            @endif
                        </div>
                    </form>
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Learners</slot:title>
            <slot:description>Open a learner to read or write what the school holds.</slot:description>
            <slot:content>
                @if ($learners->isEmpty())
                    @if ($search !== null || $missingOnly)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this search"
                            description="No learner of this school matches.">
                            <april:button-link href="{{ route('health-records.index') }}" variant="outline">Show every learner</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-heart-pulse" title="No learners yet"
                            description="Enrol a learner and their health record becomes available here." />
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Learner</april:data-table-head>
                                <april:data-table-head>Record</april:data-table-head>
                                <april:data-table-head>Emergency contact</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($learners as $learner)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $learner->user?->name ?? 'Unnamed' }}
                                        <span class="block text-xs text-muted-foreground">{{ $learner->admission_number }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        @if ($learner->healthRecord === null)
                                            <span class="text-sm text-muted-foreground">Nothing held</span>
                                        @else
                                            <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                                Saved {{ $learner->healthRecord->updated_at->format('j M Y') }}
                                            </span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">
                                        {{ $learner->healthRecord?->emergency_contact_name ?? '—' }}
                                        @if (filled($learner->healthRecord?->emergency_contact_phone))
                                            <span class="block text-xs">{{ $learner->healthRecord->emergency_contact_phone }}</span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('health-records.edit', $learner) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            Open
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>

                    <div class="pt-4">
                        {{ $learners->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
