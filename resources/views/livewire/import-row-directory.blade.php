<div class="space-y-4">
    <div class="flex flex-wrap items-end gap-3">
        <div class="flex w-full flex-col gap-2 sm:max-w-xs">
            <april:label for="filter-state">Show</april:label>
            <april:native-select id="filter-state" wire:model.live="state">
                <option value="">Every row</option>
                @foreach ($states as $availableState)
                    <option value="{{ $availableState->value }}">{{ $availableState->label() }}</option>
                @endforeach
            </april:native-select>
        </div>
        @if ($selectedState !== null)
            <april:button type="button" variant="outline" wire:click="clearFilter" wire:loading.attr="disabled">
                <x-lucide-x class="mr-2 size-4" />
                Clear filter
            </april:button>
        @endif
    </div>
    <p wire:loading class="text-sm text-muted-foreground" role="status">Updating rows…</p>

    @if ($rows->isEmpty())
        @if ($selectedState !== null)
            <x-empty-state icon="lucide-search-x" title="No row is in that state"
                description="Choose another state, or show every row.">
                <april:button type="button" variant="outline" wire:click="clearFilter">Show every row</april:button>
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

        {{ $rows->links('components.datatable-pagination-links-view') }}
    @endif
</div>
