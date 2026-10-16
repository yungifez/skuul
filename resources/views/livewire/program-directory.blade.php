<div class="space-y-6">
    <april:card>
        <slot:title>Activities learners take part in</slot:title>
        <slot:description>A club, an intervention, or a support service. Taking part never changes a learner’s enrollment.</slot:description>
        <slot:content>
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
                <div class="flex min-w-0 flex-col gap-2">
                    <april:label for="program-filter-type">Kind</april:label>
                    <select id="program-filter-type" wire:model.live="type" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">Every kind</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected($selectedType === $type)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="program-filter-active" class="flex min-h-10 items-center gap-2 text-sm">
                    <input id="program-filter-active" type="checkbox" wire:model.live="activeOnly" class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                    Only programmes still open
                </label>

                @if ($selectedType !== null || $activeOnly)
                    <div>
                        <april:button type="button" variant="outline" wire:click="clearFilters">Clear filters</april:button>
                    </div>
                @endif
            </div>
            <p wire:loading class="mt-3 text-sm text-muted-foreground" role="status">Updating programmes…</p>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Programmes</slot:title>
        <slot:description>Open a programme to read who takes part and to give somebody a place.</slot:description>
        <slot:content>
            @if ($programs->isEmpty())
                @if ($selectedType !== null || $activeOnly)
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this filter" description="No programme of that kind is open.">
                        <april:button type="button" variant="outline" wire:click="clearFilters">Show every programme</april:button>
                    </x-empty-state>
                @else
                    <x-empty-state icon="lucide-sparkles" title="No programmes yet" description="Open one to record who takes part in a club, an intervention, or a support service.">
                        @can('create', App\Models\Program::class)
                            <april:button-link href="{{ route('programs.create') }}">Open the first programme</april:button-link>
                        @endcan
                    </x-empty-state>
                @endif
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[42rem] text-left text-sm">
                        <thead>
                            <tr class="border-b border-border text-muted-foreground">
                                <th scope="col" class="px-3 py-3 font-medium">Programme</th>
                                <th scope="col" class="px-3 py-3 font-medium">Kind</th>
                                <th scope="col" class="px-3 py-3 font-medium">Taking part</th>
                                <th scope="col" class="px-3 py-3 font-medium">State</th>
                                <th scope="col" class="px-3 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programs as $program)
                                <tr wire:key="program-row-{{ $program->id }}" class="border-b border-border last:border-0">
                                    <td class="px-3 py-3 font-medium">
                                        {{ $program->name }}
                                        @if (filled($program->description))
                                            <span class="block text-xs font-normal text-muted-foreground">{{ $program->description }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-xs font-semibold">{{ $program->type->label() }}</span>
                                    </td>
                                    <td class="px-3 py-3">{{ $program->running_count }}</td>
                                    <td class="px-3 py-3 text-muted-foreground">{{ $program->is_active ? 'Open' : 'Closed' }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <april:button-link href="{{ route('programs.show', $program) }}" variant="outline" size="sm" aria-label="Open {{ $program->name }}">
                                            <x-lucide-eye class="mr-1 size-4" /> Open
                                        </april:button-link>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $programs->links('components.datatable-pagination-links-view') }}
            @endif
        </slot:content>
    </april:card>
</div>
