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
                        <dd class="text-2xl font-semibold">{{ $learnerCount - $recordedCount }}</dd>
                        <p class="mt-1 text-xs text-muted-foreground">The school holds nothing for these children</p>
                    </div>
                </dl>

                <div class="grid gap-4 border-t pt-6 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <april:label for="health-record-search">Find a learner</april:label>
                        <april:input id="health-record-search" type="search"
                            wire:model.live.debounce.300ms="search" placeholder="Name or admission number" />
                    </div>

                    <label class="flex min-h-10 items-center gap-2 text-sm">
                        <input type="checkbox" wire:model.live="missingOnly"
                            class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                        Only learners without a record
                    </label>

                    @if ($search !== '' || $missingOnly)
                        <april:button type="button" variant="outline" wire:click="clearFilters" wire:loading.attr="disabled">
                            <x-lucide-x class="mr-2 size-4" />
                            Clear filters
                        </april:button>
                    @endif
                </div>

                <p wire:loading class="text-sm text-muted-foreground" role="status">Finding learners…</p>
            </div>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Learners</slot:title>
        <slot:description>Open a learner to read or write what the school holds.</slot:description>
        <slot:content>
            @if ($learners->isEmpty())
                @if ($search !== '' || $missingOnly)
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this search"
                        description="No learner of this school matches.">
                        <april:button type="button" variant="outline" wire:click="clearFilters">Show every learner</april:button>
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
                                    <april:button-link href="{{ route('health-records.edit', $learner) }}" variant="outline" size="sm"
                                        aria-label="Open the health record for {{ $learner->user?->name ?? $learner->admission_number }}">
                                        <x-lucide-eye class="mr-1 size-4" />
                                        Open
                                    </april:button-link>
                                </april:data-table-cell>
                            </april:data-table-row>
                        @endforeach
                    </slot:body>
                </april:data-table>

                {{ $learners->links('components.datatable-pagination-links-view') }}
            @endif
        </slot:content>
    </april:card>
</div>
