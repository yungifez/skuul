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
                    <form method="POST" action="{{ route('imports.store') }}" enctype="multipart/form-data"
                        class="grid gap-4 lg:grid-cols-4 lg:items-end">
                        @csrf

                        <div class="flex flex-col gap-2">
                            <april:label for="import-type">What the file holds</april:label>
                            <april:native-select id="import-type" name="type" required>
                                <option value="">Choose an import</option>
                                @foreach ($imports as $import)
                                    <option value="{{ $import['key'] }}" @selected(old('type') === $import['key'])>
                                        {{ $import['title'] }}
                                    </option>
                                @endforeach
                            </april:native-select>
                            @error('type') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-2 lg:col-span-2">
                            <april:label for="import-file">CSV file</april:label>
                            <input type="file" id="import-file" name="file" accept=".csv,text/csv" required
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm file:mr-3 file:border-0 file:bg-transparent file:text-sm file:font-medium" />
                            <p class="text-xs text-muted-foreground">Up to 5 MB. The first line must name the columns.</p>
                            @error('file') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                        </div>

                        <april:button type="submit">
                            <x-lucide-upload class="mr-2 size-4" />
                            Check the file
                        </april:button>
                    </form>
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

        <april:card>
            <slot:title>Find an import</slot:title>
            <slot:description>Narrow the list to one kind of file or one state.</slot:description>
            <slot:content>
                <form method="GET" action="{{ route('imports.index') }}" class="grid gap-4 lg:grid-cols-4 lg:items-end">
                    <div class="flex flex-col gap-2">
                        <april:label for="filter-type">What the file held</april:label>
                        <april:native-select id="filter-type" name="type">
                            <option value="">Every import</option>
                            @foreach ($imports as $import)
                                <option value="{{ $import['key'] }}" @selected($selectedType === $import['key'])>
                                    {{ $import['title'] }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="filter-status">State</april:label>
                        <april:native-select id="filter-status" name="status">
                            <option value="">Every state</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($selectedStatus === $status)>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </april:native-select>
                    </div>

                    <div class="flex gap-2">
                        <april:button type="submit">
                            <x-lucide-filter class="mr-2 size-4" />
                            Apply
                        </april:button>
                        @if ($selectedType !== null || $selectedStatus !== null)
                            <april:button-link href="{{ route('imports.index') }}" variant="outline">Clear</april:button-link>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Imports so far</slot:title>
            <slot:description>An import keeps its rows after it runs, so the school can always see what a file changed.</slot:description>
            <slot:content>
                @if ($batches->isEmpty())
                    @if ($selectedType !== null || $selectedStatus !== null)
                        <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                            description="No import of that kind is in that state.">
                            <april:button-link href="{{ route('imports.index') }}" variant="outline">Show every import</april:button-link>
                        </x-empty-state>
                    @else
                        <x-empty-state icon="lucide-upload" title="No imports yet"
                            description="Load a file above. The application checks it and shows you what it would write before anything changes." />
                    @endif
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>File</april:data-table-head>
                                <april:data-table-head>State</april:data-table-head>
                                <april:data-table-head>Rows</april:data-table-head>
                                <april:data-table-head>Started</april:data-table-head>
                                <april:data-table-head class="text-right">Actions</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($batches as $batch)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $batch->source_name ?? 'Unnamed file' }}
                                        <span class="block text-xs text-muted-foreground">{{ $batch->type }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                            {{ $batch->status->label() }}
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell>
                                        <span class="whitespace-nowrap">{{ $batch->row_count }} read</span>
                                        <span class="block whitespace-nowrap text-xs text-muted-foreground">
                                            {{ $batch->valid_count }} ready · {{ $batch->invalid_count }} with errors
                                            @if ($batch->applied_count > 0)
                                                · {{ $batch->applied_count }} written
                                            @endif
                                        </span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                        {{ $batch->created_at->format('j M Y') }}
                                        <span class="block text-xs">{{ $batch->createdBy?->name ?? 'Unknown person' }}</span>
                                    </april:data-table-cell>
                                    <april:data-table-cell class="text-right">
                                        <april:button-link href="{{ route('imports.show', $batch) }}" variant="outline" size="sm">
                                            <x-lucide-eye class="mr-1 size-4" />
                                            View rows
                                        </april:button-link>
                                    </april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>

                    <div class="pt-4">
                        {{ $batches->links('components.pagination-links-view') }}
                    </div>
                @endif
            </slot:content>
        </april:card>
    </div>
@endsection
