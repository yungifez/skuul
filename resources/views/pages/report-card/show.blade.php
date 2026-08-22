@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('report-cards.index'), 'text' => 'Report cards'],
    ['text' => 'Issued card', 'active'],
]])

@section('title', 'Issued report card')
@section('page_heading', 'Issued report card')

@section('page_actions')
    <april:button-link href="{{ route('report-cards.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to report cards
    </april:button-link>
@endsection

@php
    $results = $reportCardSnapshot->payload['results'] ?? [];
    $latestRevision = $revisions->max('revision');
    $isSuperseded = $reportCardSnapshot->revision < $latestRevision;
@endphp

@section('content')
    <div class="space-y-6">
        @if ($isSuperseded)
            <april:alert variant="destructive">
                <slot:title>A later version exists</slot:title>
                <slot:description>
                    This is revision {{ $reportCardSnapshot->revision }}. The school has since issued revision {{ $latestRevision }}.
                    This page stays exactly as it was issued.
                </slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>{{ $reportCardSnapshot->studentRecord->user?->name ?? $reportCardSnapshot->studentRecord->admission_number }}</slot:title>
            <slot:description>{{ $reportCardSnapshot->studentRecord->admission_number }}</slot:description>
            <slot:content>
                <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border p-4">
                        <dt class="text-sm text-muted-foreground">Overall</dt>
                        <dd class="text-2xl font-semibold">
                            {{ $reportCardSnapshot->average_percentage === null ? '—' : number_format($reportCardSnapshot->average_percentage, 2).'%' }}
                        </dd>
                        <p class="mt-1 text-xs text-muted-foreground">Average across the subjects below</p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <dt class="text-sm text-muted-foreground">{{ school_term('period', 'Period') }}</dt>
                        <dd class="text-lg font-semibold">{{ $reportCardSnapshot->academicPeriod->label ?? $reportCardSnapshot->academicPeriod->name }}</dd>
                    </div>
                    <div class="rounded-lg border p-4">
                        <dt class="text-sm text-muted-foreground">Revision</dt>
                        <dd class="text-lg font-semibold">{{ $reportCardSnapshot->revision }} of {{ $latestRevision }}</dd>
                        @if (filled($reportCardSnapshot->reason))
                            <p class="mt-1 text-xs text-muted-foreground">{{ $reportCardSnapshot->reason }}</p>
                        @endif
                    </div>
                    <div class="rounded-lg border p-4">
                        <dt class="text-sm text-muted-foreground">Issued</dt>
                        <dd class="text-lg font-semibold">{{ $reportCardSnapshot->published_at->format('j M Y') }}</dd>
                        <p class="mt-1 text-xs text-muted-foreground">
                            {{ $reportCardSnapshot->publishedBy?->name ? 'By '.$reportCardSnapshot->publishedBy->name : 'Publisher not recorded' }}
                        </p>
                    </div>
                </dl>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Subject results</slot:title>
            <slot:description>Each row names the result version this card copied, so anyone can trace a mark back to its source.</slot:description>
            <slot:content>
                @if ($results === [])
                    <x-empty-state icon="lucide-book-open" title="No subject results on this card"
                        description="The card was issued before any subject result was published for the period." />
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Subject</april:data-table-head>
                                <april:data-table-head>Result</april:data-table-head>
                                <april:data-table-head>Source revision</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($results as $result)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">{{ $result['subject']['name'] }}</april:data-table-cell>
                                    <april:data-table-cell>
                                        {{ $result['percentage'] === null ? '—' : number_format($result['percentage'], 2).'%' }}
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">Revision {{ $result['source_revision'] }}</april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>
                @endif
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Revision history</slot:title>
            <slot:description>A published card never changes. A correction is issued as the next revision, and every earlier one stays readable.</slot:description>
            <slot:content>
                <ol class="space-y-3">
                    @foreach ($revisions as $revision)
                        <li class="flex flex-wrap items-center justify-between gap-3 rounded-lg border p-4 {{ $revision->is($reportCardSnapshot) ? 'border-primary' : '' }}">
                            <div>
                                <p class="font-medium">
                                    Revision {{ $revision->revision }}
                                    @if ($revision->is($reportCardSnapshot))
                                        <span class="ml-1 text-xs text-muted-foreground">(you are reading this one)</span>
                                    @endif
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{ $revision->published_at->format('j M Y') }}
                                    @if ($revision->publishedBy?->name)
                                        · {{ $revision->publishedBy->name }}
                                    @endif
                                    @if (filled($revision->reason))
                                        · {{ $revision->reason }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-muted-foreground">
                                    {{ $revision->average_percentage === null ? '—' : number_format($revision->average_percentage, 2).'%' }}
                                </span>
                                @unless ($revision->is($reportCardSnapshot))
                                    <april:button-link href="{{ route('report-cards.show', $revision) }}" variant="outline" size="sm">Open</april:button-link>
                                @endunless
                            </div>
                        </li>
                    @endforeach
                </ol>
            </slot:content>
        </april:card>
    </div>
@endsection
