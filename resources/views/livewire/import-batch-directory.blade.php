<div class="space-y-6">
    <april:card>
        <slot:title>Find an import</slot:title>
        <slot:description>Narrow the list to one kind of file or one state.</slot:description>
        <slot:content>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[minmax(12rem,1fr)_minmax(12rem,1fr)_auto] lg:items-end">
                <div class="flex flex-col gap-2">
                    <april:label for="filter-type">What the file held</april:label>
                    <april:native-select id="filter-type" wire:model.live="type">
                        <option value="">Every import</option>
                        @foreach ($imports as $import)
                            <option value="{{ $import['key'] }}">{{ $import['title'] }}</option>
                        @endforeach
                    </april:native-select>
                </div>

                <div class="flex flex-col gap-2">
                    <april:label for="filter-status">State</april:label>
                    <april:native-select id="filter-status" wire:model.live="status">
                        <option value="">Every state</option>
                        @foreach ($statuses as $availableStatus)
                            <option value="{{ $availableStatus->value }}">{{ $availableStatus->label() }}</option>
                        @endforeach
                    </april:native-select>
                </div>

                @if ($type !== '' || $selectedStatus !== null)
                    <april:button type="button" variant="outline" wire:click="clearFilters" wire:loading.attr="disabled">
                        <x-lucide-x class="mr-2 size-4" />
                        Clear filters
                    </april:button>
                @endif
            </div>
            <p wire:loading class="mt-4 text-sm text-muted-foreground" role="status">Updating imports…</p>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Imports so far</slot:title>
        <slot:description>An import keeps its rows after it runs, so the school can always see what a file changed.</slot:description>
        <slot:content>
            @if ($batches->isEmpty())
                @if ($type !== '' || $selectedStatus !== null)
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                        description="No import of that kind is in that state.">
                        <april:button type="button" variant="outline" wire:click="clearFilters">Show every import</april:button>
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
                                    <april:button-link href="{{ route('imports.show', $batch) }}" variant="outline" size="sm"
                                        aria-label="View the rows in {{ $batch->source_name ?? 'this file' }}">
                                        <x-lucide-eye class="mr-1 size-4" />
                                        View rows
                                    </april:button-link>
                                </april:data-table-cell>
                            </april:data-table-row>
                        @endforeach
                    </slot:body>
                </april:data-table>

                {{ $batches->links('components.datatable-pagination-links-view') }}
            @endif
        </slot:content>
    </april:card>
</div>
