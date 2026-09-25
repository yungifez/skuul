<div class="space-y-6">
    @if ($dueCount > 0)
        <april:alert>
            <slot:icon><x-lucide-calendar-clock class="size-4" /></slot:icon>
            <slot:title>{{ $dueCount }} {{ Str::plural('plan', $dueCount) }} {{ $dueCount === 1 ? 'is' : 'are' }} due for review</slot:title>
            <slot:description>
                A plan says when somebody should look at it again. That day has passed.
                <a class="underline" href="{{ route('support-plans.index', ['due' => 1]) }}">Show them</a>.
            </slot:description>
        </april:alert>
    @endif

    <april:card>
        <slot:title>Help agreed for one child</slot:title>
        <slot:description>
            {{ $openCount }} {{ Str::plural('plan', $openCount) }} {{ $openCount === 1 ? 'is' : 'are' }} still running.
            A health or counselling plan is readable only by the people who run it, so this list may be shorter than the school's.
        </slot:description>
        <slot:content>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <div class="flex flex-col gap-2">
                    <april:label for="filter-status">State</april:label>
                    <april:native-select id="filter-status" wire:model.live="status">
                        <option value="">Every state</option>
                        @foreach ($statuses as $availableStatus)
                            <option value="{{ $availableStatus->value }}">{{ $availableStatus->label() }}</option>
                        @endforeach
                    </april:native-select>
                </div>

                <div class="flex flex-col gap-2">
                    <april:label for="filter-category">Kind of help</april:label>
                    <april:native-select id="filter-category" wire:model.live="category">
                        <option value="">Every kind</option>
                        @foreach ($categories as $availableCategory)
                            <option value="{{ $availableCategory->value }}">{{ $availableCategory->label() }}</option>
                        @endforeach
                    </april:native-select>
                </div>

                <label class="flex min-h-10 items-center gap-2 text-sm">
                    <input type="checkbox" wire:model.live="dueOnly"
                        class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                    Only the plans due for review
                </label>

                @if ($selectedStatus !== null || $selectedCategory !== null || $dueOnly)
                    <april:button type="button" variant="outline" wire:click="clearFilters" wire:loading.attr="disabled">
                        <x-lucide-x class="mr-2 size-4" />
                        Clear filters
                    </april:button>
                @endif
            </div>

            <p wire:loading class="mt-4 text-sm text-muted-foreground" role="status">Updating plans…</p>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Plans</slot:title>
        <slot:description>Open a plan to read its steps, its notes, and how it moved.</slot:description>
        <slot:content>
            @if ($plans->isEmpty())
                @if ($selectedStatus !== null || $selectedCategory !== null || $dueOnly)
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this filter"
                        description="No plan you may read is in that state.">
                        <april:button type="button" variant="outline" wire:click="clearFilters">Show every plan</april:button>
                    </x-empty-state>
                @else
                    <x-empty-state icon="lucide-heart-handshake" title="No support plans yet"
                        description="A plan names one child, what the school agreed to do, and when somebody should look at it again.">
                        @can('create', App\Models\SupportPlan::class)
                            <april:button-link href="{{ route('support-plans.create') }}">Open the first plan</april:button-link>
                        @endcan
                    </x-empty-state>
                @endif
            @else
                <april:data-table>
                    <slot:header>
                        <april:data-table-row>
                            <april:data-table-head>Plan</april:data-table-head>
                            <april:data-table-head>Learner</april:data-table-head>
                            <april:data-table-head>Kind</april:data-table-head>
                            <april:data-table-head>State</april:data-table-head>
                            <april:data-table-head>Review</april:data-table-head>
                            <april:data-table-head class="text-right">Actions</april:data-table-head>
                        </april:data-table-row>
                    </slot:header>
                    <slot:body>
                        @foreach ($plans as $plan)
                            <april:data-table-row>
                                <april:data-table-cell class="font-medium">
                                    {{ $plan->title }}
                                    <span class="block text-xs text-muted-foreground">
                                        {{ $plan->actions_count }} {{ Str::plural('step', $plan->actions_count) }}
                                        · {{ $plan->notes_count }} {{ Str::plural('note', $plan->notes_count) }}
                                    </span>
                                </april:data-table-cell>
                                <april:data-table-cell>
                                    {{ $plan->studentRecord->user?->name ?? $plan->studentRecord->admission_number }}
                                    <span class="block text-xs text-muted-foreground">{{ $plan->studentRecord->admission_number }}</span>
                                </april:data-table-cell>
                                <april:data-table-cell>
                                    <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                        {{ $plan->category->label() }}
                                    </span>
                                    @if ($plan->is_confidential)
                                        <span class="mt-1 flex items-center gap-1 text-xs text-muted-foreground">
                                            <x-lucide-lock class="size-3" />
                                            Confidential
                                        </span>
                                    @endif
                                </april:data-table-cell>
                                <april:data-table-cell>
                                    <span class="inline-flex whitespace-nowrap items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">
                                        {{ $plan->status->label() }}
                                    </span>
                                </april:data-table-cell>
                                <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                    {{ $plan->review_on?->format('j M Y') ?? 'No date' }}
                                </april:data-table-cell>
                                <april:data-table-cell class="text-right">
                                    <april:button-link href="{{ route('support-plans.show', $plan) }}" variant="outline" size="sm"
                                        aria-label="Open {{ $plan->title }}">
                                        <x-lucide-eye class="mr-1 size-4" />
                                        Open
                                    </april:button-link>
                                </april:data-table-cell>
                            </april:data-table-row>
                        @endforeach
                    </slot:body>
                </april:data-table>

                {{ $plans->links('components.datatable-pagination-links-view') }}
            @endif
        </slot:content>
    </april:card>
</div>
