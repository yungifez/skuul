@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('imports.index'), 'text' => 'Imports', 'active'],
]])

@section('title', 'Imports')
@section('page_heading', 'Imports')

@section('content')
    <div class="space-y-6">
        @can('create', App\Models\ImportBatch::class)
            <april:card>
                <slot:title>Import a file</slot:title>
                <slot:description>The file is checked first and nothing is written. You read what each row will do, then you choose to write it.</slot:description>
                <slot:content>
                    @livewire('import-file-form')
                </slot:content>
            </april:card>
        @endcan

        <april:card>
            <slot:title>What each file must hold</slot:title>
            <slot:description>Name the columns on the first line. The order does not matter, and the names are read without regard to case.</slot:description>
            <slot:content>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($imports as $import)
                        <div class="rounded-lg border p-4">
                            <p class="font-medium">{{ $import['title'] }}</p>
                            <dl class="mt-3 space-y-3 text-sm">
                                <div>
                                    <dt class="text-muted-foreground">Required columns</dt>
                                    <dd class="mt-1 flex flex-wrap gap-1">
                                        @foreach ($import['required'] as $column)
                                            <code class="rounded bg-muted px-1.5 py-0.5 text-xs">{{ $column }}</code>
                                        @endforeach
                                    </dd>
                                </div>
                                @if ($import['optional'] !== [])
                                    <div>
                                        <dt class="text-muted-foreground">Optional columns</dt>
                                        <dd class="mt-1 flex flex-wrap gap-1">
                                            @foreach ($import['optional'] as $column)
                                                <code class="rounded bg-muted px-1.5 py-0.5 text-xs">{{ $column }}</code>
                                            @endforeach
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                            <p class="mt-3 text-xs text-muted-foreground">
                                Give a row a <code class="rounded bg-muted px-1 py-0.5">source_id</code> to import the same
                                file twice without making a second record.
                            </p>
                        </div>
                    @endforeach
                </div>
            </slot:content>
        </april:card>

        @livewire('import-batch-directory')
    </div>
@endsection
