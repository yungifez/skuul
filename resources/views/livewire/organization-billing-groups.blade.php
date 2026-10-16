<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <p class="text-sm text-muted-foreground">
        Campuses in one group bill a family as one school. A learner who moves between them takes what they owe along, and the campuses settle with each other.
        A campus on its own keeps what it is owed. Changing a group moves nothing already in the books.
    </p>

    <section class="flex flex-col gap-2" aria-labelledby="campuses-heading">
        <h2 id="campuses-heading" class="text-base font-semibold">Campuses</h2>
        @if ($campuses->isEmpty())
            <p class="text-sm text-muted-foreground">This organization has no campuses yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($campuses as $campus)
                    <li wire:key="campus-{{ $campus->id }}" class="flex flex-col gap-2 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <label for="group-{{ $campus->id }}" class="min-w-0 truncate text-sm font-medium">{{ $campus->name }}</label>
                        <select id="group-{{ $campus->id }}" wire:change="placeCampus({{ $campus->id }}, $event.target.value)" wire:loading.attr="disabled" class="{{ $controlClasses }} sm:w-64">
                            <option value="">Bills on its own</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}" @selected($campus->billing_group_id === $group->id)>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="groups-heading">
        <h2 id="groups-heading" class="text-base font-semibold">Groups</h2>
        @if ($groups->isNotEmpty())
            <ul class="divide-y border-y">
                @foreach ($groups as $group)
                    <li wire:key="billing-group-{{ $group->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $group->name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $group->schools->isEmpty() ? '—' : $group->schools->sortBy('name')->pluck('name')->join(', ', ' and ') }}</p>
                        </div>
                        @if ($group->schools->isEmpty())
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $group->name }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="deleteGroup({{ $group->id }})" wire:confirm="Delete {{ $group->name }}?"><x-lucide-trash-2 class="mr-2 size-4" />Delete</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <form wire:submit="startGroup" class="flex flex-col gap-2 sm:flex-row sm:items-start" aria-label="Start a group">
            <div class="min-w-0 flex-1">
                <label for="group-name" class="sr-only">Name of the new group</label>
                <input id="group-name" wire:model="name" required maxlength="100" placeholder="City campuses" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
                <x-field-error name="name" class="mt-1" />
            </div>
            <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="startGroup">
                <x-lucide-plus class="mr-2 size-4" aria-hidden="true" />Start a group
            </april:button>
        </form>
    </section>
</div>
