@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('imports.index'), 'text' => 'Imports'],
    ['text' => $batch->source_name ?? 'Import', 'active'],
]])

@section('title', 'Import')
@section('page_heading', $batch->source_name ?? 'Import')

@section('page_actions')
    <april:button-link href="{{ route('imports.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to imports
    </april:button-link>
@endsection

@php
    $states = App\Enums\ImportRowState::cases();
    $figures = [
        ['label' => 'Rows read', 'value' => $batch->row_count, 'hint' => 'Lines of data in the file'],
        ['label' => 'Ready', 'value' => $batch->valid_count, 'hint' => 'Rows that passed every check'],
        ['label' => 'With errors', 'value' => $batch->invalid_count, 'hint' => 'Rows that will not be written'],
        ['label' => 'Written', 'value' => $batch->applied_count, 'hint' => 'Records this import saved'],
    ];
@endphp

@section('content')
    <div class="space-y-6">
        @if ($batch->status->canBeApplied() && $batch->valid_count > 0)
            <april:alert>
                <slot:title>Nothing is written yet</slot:title>
                <slot:description>
                    {{ $batch->valid_count }} {{ Str::plural('row', $batch->valid_count) }} passed the check. Read them
                    below, then write the import. Rows with errors are left alone.
                </slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>What this file will do</slot:title>
            <slot:description>
                {{ $batch->type }} · started {{ $batch->created_at->format('j M Y') }}
                by {{ $batch->createdBy?->name ?? 'an unknown person' }}
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($figures as $figure)
                            <div class="rounded-lg border p-4">
                                <dt class="text-sm text-muted-foreground">{{ $figure['label'] }}</dt>
                                <dd class="text-2xl font-semibold">{{ $figure['value'] }}</dd>
                                <p class="mt-1 text-xs text-muted-foreground">{{ $figure['hint'] }}</p>
                            </div>
                        @endforeach
                    </dl>

                    <div class="flex flex-wrap items-center gap-3 border-t pt-4">
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                            {{ $batch->status->label() }}
                        </span>

                        @can('apply', $batch)
                            @if ($batch->status->canBeApplied())
                                @if ($batch->valid_count > 0)
                                    <form method="POST" action="{{ route('imports.apply', $batch) }}">
                                        @csrf
                                        <april:button type="submit">
                                            <x-lucide-database-backup class="mr-2 size-4" />
                                            Write {{ $batch->valid_count }} {{ Str::plural('row', $batch->valid_count) }}
                                        </april:button>
                                    </form>
                                @else
                                    <span class="text-sm text-muted-foreground">No row passed the check, so there is nothing to write.</span>
                                @endif

                                <form method="POST" action="{{ route('imports.cancel', $batch) }}">
                                    @csrf
                                    <april:button type="submit" variant="outline">Drop this import</april:button>
                                </form>
                            @elseif ($batch->applied_at !== null)
                                <span class="text-sm text-muted-foreground">
                                    Written on {{ $batch->applied_at->format('j M Y') }}. An import runs once.
                                </span>
                            @else
                                <span class="text-sm text-muted-foreground">This import is finished. Load the file again to run it.</span>
                            @endif
                        @endcan
                    </div>
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Rows</slot:title>
            <slot:description>Every line the file held, and what the application found wrong with it.</slot:description>
            <slot:content>
                <div class="space-y-4">
                    <form method="GET" action="{{ route('imports.show', $batch) }}" class="flex flex-wrap items-end gap-2">
                        <div class="flex flex-col gap-2">
                            <april:label for="filter-state">Show</april:label>
                            <april:native-select id="filter-state" name="state">
                                <option value="">Every row</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->value }}" @selected($selectedState === $state->value)>
                                        {{ $state->label() }}
                                    </option>
                                @endforeach
                            </april:native-select>
                        </div>
                        <april:button type="submit">
                            <x-lucide-filter class="mr-2 size-4" />
                            Apply
                        </april:button>
                        @if ($selectedState !== null)
                            <april:button-link href="{{ route('imports.show', $batch) }}" variant="outline">Clear</april:button-link>
                        @endif
                    </form>

                    @if ($rows->isEmpty())
                        @if ($selectedState !== null)
                            <x-empty-state icon="lucide-search-x" title="No row is in that state"
                                description="Choose another state, or show every row.">
                                <april:button-link href="{{ route('imports.show', $batch) }}" variant="outline">Show every row</april:button-link>
                            </x-empty-state>
                        @else
                            <x-empty-state icon="lucide-file-x" title="This import has no rows"
                                description="The file held a heading line and nothing else." />
                        @endif
                    @else
                        <div class="overflow-x-auto">
                            <april:data-table>
                                <slot:header>
                                    <april:data-table-row>
                                        <april:data-table-head>Line</april:data-table-head>
                                        <april:data-table-head>State</april:data-table-head>
                                        @foreach ($columns as $column)
                                            <april:data-table-head>{{ $column }}</april:data-table-head>
                                        @endforeach
                                        <april:data-table-head>What is wrong</april:data-table-head>
                                    </april:data-table-row>
                                </slot:header>
                                <slot:body>
                                    @foreach ($rows as $row)
                                        <april:data-table-row>
                                            <april:data-table-cell class="font-medium">{{ $row->line_number }}</april:data-table-cell>
                                            <april:data-table-cell>
                                                <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                                    {{ $row->state->label() }}
                                                </span>
                                            </april:data-table-cell>
                                            @foreach ($columns as $column)
                                                <april:data-table-cell class="whitespace-nowrap">
                                                    {{ $row->payload[$column] ?? '—' }}
                                                </april:data-table-cell>
                                            @endforeach
                                            <april:data-table-cell>
                                                @if (blank($row->errors))
                                                    <span class="text-muted-foreground">—</span>
                                                @else
                                                    <ul class="space-y-1 text-sm text-destructive">
                                                        @foreach ($row->errors as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </april:data-table-cell>
                                        </april:data-table-row>
                                    @endforeach
                                </slot:body>
                            </april:data-table>
                        </div>

                        <div class="pt-4">
                            {{ $rows->links('components.pagination-links-view') }}
                        </div>
                    @endif
                </div>
            </slot:content>
        </april:card>
    </div>
@endsection
