<div class="space-y-6">
    <april:card>
        <slot:title>Who works here</slot:title>
        <slot:description>
            An employment record belongs to one school. A person who works in two schools holds two records,
            so leave in one school never hides them in the other.
        </slot:description>
        <slot:content class="space-y-6">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border p-4">
                    <dt class="text-sm text-muted-foreground">Working now</dt>
                    <dd class="text-2xl font-semibold">{{ $employedCount }}</dd>
                </div>
                <div class="rounded-lg border p-4">
                    <dt class="text-sm text-muted-foreground">Away today</dt>
                    <dd class="text-2xl font-semibold">{{ $awayCount }}</dd>
                    <p class="mt-1 text-xs text-muted-foreground">Leave the school agreed to</p>
                </div>
            </dl>

            <div class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                <div class="flex flex-col gap-2">
                    <april:label for="staff-search">Find a person</april:label>
                    <april:input id="staff-search" type="search" wire:model.live.debounce.400ms="search" placeholder="Name, job, or department" />
                </div>

                <div class="flex flex-col gap-2">
                    <april:label for="staff-filter-status">State</april:label>
                    <april:native-select id="staff-filter-status" wire:model.live="status">
                        <option value="">Every state</option>
                        @foreach ($statuses as $staffStatus)
                            <option value="{{ $staffStatus->value }}">{{ $staffStatus->label() }}</option>
                        @endforeach
                    </april:native-select>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model.live="awayOnly" class="size-4 rounded border-input text-primary-foreground focus:ring-2 focus:ring-ring">
                    Only the people away today
                </label>

                @if ($search !== '' || $selectedStatus !== null || $awayOnly)
                    <div>
                        <april:button type="button" variant="outline" wire:click="clearFilters">Clear filters</april:button>
                    </div>
                @endif
            </div>
            <p wire:loading class="text-sm text-muted-foreground" role="status">Updating staff list…</p>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Employment records</slot:title>
        <slot:description>Open a record to read the job, the qualifications, the working hours, and the leave.</slot:description>
        <slot:content class="space-y-4">
            @forelse ($profiles as $profile)
                <article class="rounded-lg border p-4" wire:key="staff-profile-{{ $profile->id }}">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                        <div>
                            <h3 class="font-semibold">{{ $profile->user?->name ?? 'Unnamed' }}</h3>
                            <p class="mt-1 text-sm text-muted-foreground">
                                {{ $profile->staff_number ?? 'No staff number' }}
                                · {{ $profile->credentials_count }} {{ Str::plural('qualification', $profile->credentials_count) }}
                            </p>
                        </div>
                        <span class="inline-flex w-fit items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold">{{ $profile->status->label() }}</span>
                    </div>

                    <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <dt class="text-xs font-medium text-muted-foreground">Job</dt>
                            <dd class="mt-1 text-sm">{{ $profile->job_title ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-muted-foreground">Department</dt>
                            <dd class="mt-1 text-sm">{{ $profile->department ?? 'No department' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-muted-foreground">Employment type</dt>
                            <dd class="mt-1 text-sm">{{ $profile->employment_type->label() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-muted-foreground">Joined</dt>
                            <dd class="mt-1 text-sm">{{ $profile->joined_on?->format('j M Y') ?? 'No date' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4">
                        <april:button-link href="{{ route('staff-profiles.show', $profile) }}" variant="outline" size="sm"
                            aria-label="Open the profile of {{ $profile->user?->name ?? $profile->staff_number ?? 'this member of staff' }}">
                            <x-lucide-eye class="mr-1 size-4" /> Open profile
                        </april:button-link>
                    </div>
                </article>
            @empty
                @if ($search !== '' || $selectedStatus !== null || $awayOnly)
                    <x-empty-state icon="lucide-search-x" title="Nothing matches this search" description="No employment record of this school matches.">
                        <april:button type="button" variant="outline" wire:click="clearFilters">Show every record</april:button>
                    </x-empty-state>
                @else
                    <x-empty-state icon="lucide-briefcase" title="No employment records yet" description="Add a record for each person who works here, or import them from a file.">
                        @can('create', App\Models\StaffProfile::class)
                            <april:button-link href="{{ route('staff-profiles.create') }}">Add the first record</april:button-link>
                        @endcan
                    </x-empty-state>
                @endif
            @endforelse

            {{ $profiles->links('components.datatable-pagination-links-view') }}
        </slot:content>
    </april:card>
</div>
