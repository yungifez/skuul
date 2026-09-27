<div class="flex flex-col gap-10">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <p class="text-sm text-muted-foreground">
        A shared residence is one site whose houses belong to different campuses. Records, rooms, beds and staff stay with each campus.
    </p>

    @forelse ($residences as $residence)
        @php
            $linkedCampusIds = $residence->schools->pluck('id');
            $campusesToLink = $campuses->whereNotIn('id', $linkedCampusIds);
            $housesToAdd = $availableHouses->whereIn('school_id', $linkedCampusIds);
        @endphp
        <section wire:key="residence-{{ $residence->id }}" class="flex flex-col gap-4" aria-labelledby="residence-{{ $residence->id }}-heading">
            <div>
                <h2 id="residence-{{ $residence->id }}-heading" class="text-base font-semibold">{{ $residence->name }}</h2>
                <p class="text-sm text-muted-foreground">
                    {{ $residence->schools->count() }} {{ Str::plural('campus', $residence->schools->count()) }} · {{ $residence->dormitories->count() }} {{ Str::plural('house', $residence->dormitories->count()) }}{{ $residence->is_active ? '' : ' · not in use' }}{{ $residence->notes ? ' · '.$residence->notes : '' }}
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <h3 class="text-sm font-medium">Campuses</h3>
                    @if ($residence->schools->isNotEmpty())
                        <ul class="divide-y border-y">
                            @foreach ($residence->schools as $campus)
                                @php($hasHouses = $residence->dormitories->contains('school_id', $campus->id))
                                <li wire:key="residence-{{ $residence->id }}-campus-{{ $campus->id }}" class="flex min-h-11 items-center gap-3 py-1">
                                    <p class="min-w-0 flex-1 truncate text-sm">{{ $campus->name }}</p>
                                    @if ($hasHouses)
                                        <span class="text-xs text-muted-foreground">Has houses here</span>
                                    @else
                                        <april:dropdown-menu>
                                            <slot:trigger>
                                                <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $campus->name }}">
                                                    <x-lucide-ellipsis class="size-4" />
                                                </april:button>
                                            </slot:trigger>
                                            <slot:content align="end">
                                                <april:dropdown-menu-item wire:click="unlinkCampus({{ $residence->id }}, {{ $campus->id }})" wire:confirm="Stop {{ $campus->name }} using {{ $residence->name }}?"><x-lucide-unlink class="mr-2 size-4" />Stop using this residence</april:dropdown-menu-item>
                                            </slot:content>
                                        </april:dropdown-menu>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($campusesToLink->isNotEmpty())
                        <label for="campus-{{ $residence->id }}" class="sr-only">Link a campus to {{ $residence->name }}</label>
                        <select id="campus-{{ $residence->id }}" wire:change="linkCampus({{ $residence->id }}, $event.target.value)" wire:loading.attr="disabled" class="{{ $controlClasses }}">
                            <option value="">Link a campus…</option>
                            @foreach ($campusesToLink as $campus)
                                <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="flex flex-col gap-2">
                    <h3 class="text-sm font-medium">Houses</h3>
                    @if ($residence->dormitories->isNotEmpty())
                        <ul class="divide-y border-y">
                            @foreach ($residence->dormitories as $dormitory)
                                <li wire:key="residence-{{ $residence->id }}-house-{{ $dormitory->id }}" class="flex min-h-11 items-center gap-3 py-1">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm">{{ $dormitory->name }}</p>
                                        <p class="truncate text-xs text-muted-foreground">{{ $dormitory->school?->name ?? '—' }}</p>
                                    </div>
                                    <april:dropdown-menu>
                                        <slot:trigger>
                                            <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $dormitory->name }}">
                                                <x-lucide-ellipsis class="size-4" />
                                            </april:button>
                                        </slot:trigger>
                                        <slot:content align="end">
                                            <april:dropdown-menu-item wire:click="detachHouse({{ $residence->id }}, {{ $dormitory->id }})" wire:confirm="Take {{ $dormitory->name }} out of {{ $residence->name }}? The house and its rooms stay."><x-lucide-log-out class="mr-2 size-4" />Take out of this residence</april:dropdown-menu-item>
                                        </slot:content>
                                    </april:dropdown-menu>
                                </li>
                            @endforeach
                        </ul>
                    @elseif ($residence->schools->isEmpty())
                        <p class="text-sm text-muted-foreground">Link a campus first. Its houses can then go in.</p>
                    @endif
                    @if ($housesToAdd->isNotEmpty())
                        <label for="house-{{ $residence->id }}" class="sr-only">Add a house to {{ $residence->name }}</label>
                        <select id="house-{{ $residence->id }}" wire:change="attachHouse({{ $residence->id }}, $event.target.value)" wire:loading.attr="disabled" class="{{ $controlClasses }}">
                            <option value="">Add a house…</option>
                            @foreach ($housesToAdd as $dormitory)
                                <option value="{{ $dormitory->id }}">{{ $dormitory->name }} · {{ $dormitory->school?->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </section>
    @empty
        <p class="text-sm text-muted-foreground">No shared residence. Each campus uses its own houses.</p>
    @endforelse

    <form wire:submit="createResidence" class="flex flex-col gap-3 border-t pt-6" aria-label="Add a shared residence">
        <h2 class="text-base font-semibold">Add a shared residence</h2>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="residence-name" class="text-sm text-muted-foreground">Name</label>
                <input id="residence-name" wire:model="name" required maxlength="100" placeholder="North campus residence" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('name') }}>
                <x-field-error name="name" class="mt-1" />
            </div>
            <div>
                <label for="residence-notes" class="text-sm text-muted-foreground">Notes (optional)</label>
                <input id="residence-notes" wire:model="notes" maxlength="1000" placeholder="Shared by two campuses" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('notes') }}>
                <x-field-error name="notes" class="mt-1" />
            </div>
        </div>
        <div class="flex justify-end">
            <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="createResidence">Add the residence</april:button>
        </div>
    </form>
</div>
