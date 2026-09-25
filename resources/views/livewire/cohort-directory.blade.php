<div class="space-y-6">
    <april:card>
        <slot:title>A group that is not a class</slot:title>
        <slot:description>A graduation year, a scholarship group, a club, or a watchlist. Past membership stays visible when somebody leaves.</slot:description>
        <slot:content>
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
                <div class="flex min-w-0 flex-col gap-2">
                    <april:label for="filter-type">Kind of group</april:label>
                    <select id="filter-type" wire:model.live="type" class="h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">Every kind</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected($selectedType === $type)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <label for="filter-active" class="flex min-h-10 items-center gap-2 text-sm">
                    <input id="filter-active" type="checkbox" wire:model.live="activeOnly" class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                    Only groups still in use
                </label>

                @if ($selectedType !== null || $activeOnly)
                    <div>
                        <april:button type="button" variant="outline" wire:click="clearFilters">Clear filters</april:button>
                    </div>
                @endif
            </div>
            <p wire:loading class="mt-3 text-sm text-muted-foreground" role="status">Updating groups…</p>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Groups</slot:title>
        <slot:description>Open a group to read who is in it and to add somebody.</slot:description>
        <slot:content>
            @if ($cohorts->isEmpty())
                @if ($selectedType !== null || $activeOnly)
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this filter" description="No group you may read is of that kind.">
                        <april:button type="button" variant="outline" wire:click="clearFilters">Show every group</april:button>
                    </x-empty-state>
                @else
                    <x-empty-state icon="lucide-users-round" title="No groups yet"
                        description="Make a group to follow learners across {{ strtolower(school_terms('class_level', 'classes')) }} and {{ strtolower(school_terms('academic_year', 'school years')) }}.">
                        @can('create', App\Models\Cohort::class)
                            <april:button-link href="{{ route('cohorts.create') }}">Make the first group</april:button-link>
                        @endcan
                    </x-empty-state>
                @endif
            @else
                <div class="grid gap-3 md:hidden">
                    @foreach ($cohorts as $cohort)
                        <article wire:key="cohort-mobile-{{ $cohort->id }}" class="rounded-lg border border-border bg-background p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="break-words font-medium leading-6">{{ $cohort->name }}</h3>
                                    @if (filled($cohort->description))
                                        <p class="mt-1 break-words text-sm text-muted-foreground">{{ $cohort->description }}</p>
                                    @endif
                                </div>
                                <span class="shrink-0 text-sm text-muted-foreground">{{ $cohort->is_active ? 'In use' : 'Closed' }}</span>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 border-t border-border pt-4 text-sm">
                                <div class="min-w-0">
                                    <dt class="text-muted-foreground">Kind</dt>
                                    <dd class="mt-1 break-words font-medium">{{ $cohort->type->label() }}</dd>
                                </div>
                                <div class="min-w-0">
                                    <dt class="text-muted-foreground">In it now</dt>
                                    <dd class="mt-1 font-medium">{{ $cohort->current_members_count }}</dd>
                                </div>
                                @if ($cohort->is_restricted)
                                    <div class="col-span-2 flex items-center gap-1 text-muted-foreground">
                                        <x-lucide-lock class="size-3" />
                                        <dt class="sr-only">Access</dt>
                                        <dd>Private group</dd>
                                    </div>
                                @endif
                            </dl>

                            <april:button-link href="{{ route('cohorts.show', $cohort) }}" variant="outline" class="mt-4 w-full justify-center" aria-label="Open {{ $cohort->name }}">
                                <x-lucide-eye class="mr-1 size-4" /> Open group
                            </april:button-link>
                        </article>
                    @endforeach
                </div>

                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-border text-muted-foreground">
                                <th scope="col" class="px-3 py-3 font-medium">Group</th>
                                <th scope="col" class="px-3 py-3 font-medium">Kind</th>
                                <th scope="col" class="px-3 py-3 font-medium">In it now</th>
                                <th scope="col" class="px-3 py-3 font-medium">State</th>
                                <th scope="col" class="px-3 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cohorts as $cohort)
                                <tr wire:key="cohort-row-{{ $cohort->id }}" class="border-b border-border last:border-0">
                                    <td class="px-3 py-3 font-medium">
                                        {{ $cohort->name }}
                                        @if (filled($cohort->description))
                                            <span class="block text-xs text-muted-foreground">{{ $cohort->description }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-xs font-semibold">{{ $cohort->type->label() }}</span>
                                        @if ($cohort->is_restricted)
                                            <span class="mt-1 flex items-center gap-1 text-xs text-muted-foreground"><x-lucide-lock class="size-3" /> Private</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">{{ $cohort->current_members_count }}</td>
                                    <td class="px-3 py-3 text-muted-foreground">{{ $cohort->is_active ? 'In use' : 'Closed' }}</td>
                                    <td class="px-3 py-3 text-right">
                                        <april:button-link href="{{ route('cohorts.show', $cohort) }}" variant="outline" size="sm" aria-label="Open {{ $cohort->name }}">
                                            <x-lucide-eye class="mr-1 size-4" /> Open
                                        </april:button-link>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $cohorts->links('components.datatable-pagination-links-view') }}
            @endif
        </slot:content>
    </april:card>
</div>
