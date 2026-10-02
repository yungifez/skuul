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
                {{ $batch->type }} · started {{ school_time($batch->created_at)?->format('j M Y') }}
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

                    @livewire('import-batch-actions', ['batch' => $batch])
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Rows</slot:title>
            <slot:description>Every line the file held, and what the application found wrong with it.</slot:description>
            <slot:content>
                @livewire('import-row-directory', ['batch' => $batch])
            </slot:content>
        </april:card>
    </div>
@endsection
