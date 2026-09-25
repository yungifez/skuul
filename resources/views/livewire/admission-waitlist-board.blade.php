<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Places for full {{ strtolower(school_terms('section', 'sections')) }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">Capacity is enforced when a learner is placed. The queue keeps priority and decision history until a place is accepted.</p>
    </div>

    <x-display-validation-errors />

    @can('create', \App\Models\AdmissionWaitlistEntry::class)
        <april:card>
            <slot:title>Add a candidate</slot:title>
            <slot:description>Only {{ strtolower(school_terms('section', 'sections')) }} that have reached their configured capacity appear here.</slot:description>
            <slot:content>
                <form wire:submit="addCandidate" class="grid gap-4 md:grid-cols-4 md:items-end">
                    <div class="min-w-0 flex flex-col gap-2 md:col-span-2">
                        <label for="waitlist-section" class="text-sm font-medium">{{ school_term('section', 'Section') }}</label>
                        <select id="waitlist-section" wire:model="academic_cycle_section_id" required class="h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 text-sm">
                            <option value="">Choose a full section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->academicLevel->name }} · {{ $section->label ?? $section->name }} · {{ $section->academicYear->name }} ({{ $section->capacity }})</option>
                            @endforeach
                        </select>
                        <x-field-error name="academic_cycle_section_id" />
                    </div>
                    <div class="min-w-0 flex flex-col gap-2">
                        <label for="waitlist-candidate" class="text-sm font-medium">Candidate</label>
                        <select id="waitlist-candidate" wire:model="user_id" required class="h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 text-sm">
                            <option value="">Choose a candidate</option>
                            @foreach ($candidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->name }} · {{ $candidate->email }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="user_id" />
                    </div>
                    <div class="min-w-0 flex flex-col gap-2">
                        <label for="waitlist-priority" class="text-sm font-medium">Priority</label>
                        <input id="waitlist-priority" wire:model="priority" type="number" min="0" max="9999" class="h-10 w-full min-w-0 rounded-md border border-input bg-background px-3 text-sm">
                        <x-field-error name="priority" />
                    </div>
                    <div class="md:col-span-4">
                        <april:button type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Add to waitlist</span>
                            <span wire:loading>Adding…</span>
                        </april:button>
                    </div>
                </form>
            </slot:content>
        </april:card>
    @endcan

    <april:card>
        <slot:title>Queue</slot:title>
        <slot:description>Higher priority is offered first. Within the same priority, the original position is kept.</slot:description>
        <slot:content>
            @if ($entries->isEmpty())
                <x-empty-state icon="lucide-users" title="No waitlist entries" description="Candidates added to a full section will appear here." />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                                <th class="p-3 font-medium">Candidate</th>
                                <th class="p-3 font-medium">Section</th>
                                <th class="p-3 font-medium">Priority</th>
                                <th class="p-3 font-medium">Position</th>
                                <th class="p-3 font-medium">Status</th>
                                <th class="p-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entries as $entry)
                                <tr wire:key="waitlist-entry-{{ $entry->id }}" class="border-b last:border-0">
                                    <td class="p-3 font-medium">{{ $entry->candidate?->name }}</td>
                                    <td class="p-3">{{ $entry->academicCycleSection?->academicLevel?->name }} · {{ $entry->academicCycleSection?->name }}</td>
                                    <td class="p-3">{{ $entry->priority }}</td>
                                    <td class="p-3">{{ $entry->position }}</td>
                                    <td class="p-3">{{ $entry->status->label() }}</td>
                                    <td class="p-3 text-right">
                                        @can('update', $entry)
                                            @if ($entry->status === \App\Enums\AdmissionWaitlistStatus::Pending)
                                                <april:button type="button" size="sm" variant="outline" wire:click="offer({{ $entry->id }})" wire:loading.attr="disabled">Offer place</april:button>
                                            @elseif ($entry->status === \App\Enums\AdmissionWaitlistStatus::Offered)
                                                <april:button type="button" size="sm" wire:click="accept({{ $entry->id }})" wire:loading.attr="disabled">Accept and enrol</april:button>
                                            @endif
                                            @if ($entry->isOpen())
                                                <april:button type="button" size="sm" variant="ghost" wire:click="decline({{ $entry->id }})" wire:confirm="Decline this admission place?" wire:loading.attr="disabled">Decline</april:button>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </slot:content>
    </april:card>
</div>
