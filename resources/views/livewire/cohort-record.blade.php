<div class="flex flex-col gap-10">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $holderName = fn ($member) => $member->studentRecord?->user?->name ?? $member->user?->name ?? '—';
    @endphp

    <section class="flex flex-col gap-3" aria-labelledby="group-heading">
        <div class="flex items-center justify-between gap-3">
            <h2 id="group-heading" class="text-base font-semibold">The group</h2>
            @if ($canWrite && !$isEditing)
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="startEditing">
                    <x-lucide-pencil class="mr-2 size-4" aria-hidden="true" />Change
                </april:button>
            @endif
        </div>

        @if ($isEditing)
            <form wire:submit="save" class="flex flex-col gap-3" aria-label="Change the group">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="name" class="text-sm text-muted-foreground">Name</label>
                        <input id="name" wire:model="name" required maxlength="100" class="{{ $controlClasses }}" {{ field_error_bindings('name') }}>
                        <x-field-error name="name" class="mt-1" />
                    </div>
                    <div>
                        <label for="description" class="text-sm text-muted-foreground">What it is for (optional)</label>
                        <input id="description" wire:model="description" maxlength="1000" class="{{ $controlClasses }}" {{ field_error_bindings('description') }}>
                        <x-field-error name="description" class="mt-1" />
                    </div>
                </div>
                <label class="flex min-h-11 select-none items-center gap-3 text-sm">
                    <input type="checkbox" wire:model="isActive" class="size-5 rounded border-input">
                    Still in use. A closed group takes nobody new.
                </label>
                <div class="flex justify-end gap-2">
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopEditing">Cancel</april:button>
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save</april:button>
                </div>
            </form>
        @else
            <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
                <div>
                    <dt class="text-muted-foreground">Kind</dt>
                    <dd class="flex items-center gap-1">
                        {{ $cohort->type->label() }}
                        @if ($cohort->is_restricted)
                            <x-lucide-lock class="size-3" aria-hidden="true" /><span class="sr-only">(private)</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">State</dt>
                    <dd>{{ $cohort->is_active ? 'In use' : 'Closed' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">In it now</dt>
                    <dd>{{ $current->count() }}</dd>
                </div>
                <div class="sm:col-span-3">
                    <dt class="text-muted-foreground">What it is for</dt>
                    <dd>{{ $cohort->description ?? '—' }}</dd>
                </div>
            </dl>
        @endif
    </section>

    <section class="flex flex-col gap-3" aria-labelledby="members-heading">
        <h2 id="members-heading" class="text-base font-semibold">Who is in it</h2>

        @if ($current->isEmpty())
            <p class="text-sm text-muted-foreground">Nobody yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($current as $member)
                    <li wire:key="member-{{ $member->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $holderName($member) }}</p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ $member->studentRecord?->admission_number ?? '—' }} · joined {{ $member->joined_on?->format('j M Y') ?? '—' }}
                            </p>
                        </div>
                        @if ($canWrite)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $holderName($member) }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="removeMember({{ $member->id }})" wire:confirm="Take {{ $holderName($member) }} out of {{ $cohort->name }}? The group keeps the place they held."><x-lucide-user-minus class="mr-2 size-4" />Take out</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canWrite && $cohort->is_active)
            <form wire:submit="addMember" class="grid gap-3 sm:grid-cols-[1fr_12rem_auto] sm:items-end" aria-label="Add a learner to the group">
                <div>
                    <label for="student_record_id" class="text-sm text-muted-foreground">Learner</label>
                    <select id="student_record_id" wire:model="studentRecordId" required class="{{ $controlClasses }}" {{ field_error_bindings('studentRecordId') }}>
                        <option value="">Choose a learner</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user?->name ?? '—' }} · {{ $student->admission_number }}</option>
                        @endforeach
                    </select>
                    <x-field-error name="studentRecordId" class="mt-1" />
                </div>
                <div>
                    <label for="joined_on" class="text-sm text-muted-foreground">Joined on</label>
                    <input type="date" id="joined_on" wire:model="joinedOn" required max="{{ now()->toDateString() }}" class="{{ $controlClasses }}" {{ field_error_bindings('joinedOn') }}>
                    <x-field-error name="joinedOn" class="mt-1" />
                </div>
                <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="addMember">
                    <x-lucide-user-plus class="mr-2 size-4" aria-hidden="true" />Add
                </april:button>
            </form>
        @elseif ($canWrite)
            <p class="text-sm text-muted-foreground">The group is closed, so nobody new joins.</p>
        @endif
    </section>

    @if ($past->isNotEmpty())
        <section class="flex flex-col gap-3" aria-labelledby="past-heading">
            <h2 id="past-heading" class="text-base font-semibold">Who has left</h2>
            <ul class="divide-y border-y">
                @foreach ($past as $member)
                    <li wire:key="past-{{ $member->id }}" class="py-3 text-sm">
                        <p class="truncate font-medium">{{ $holderName($member) }}</p>
                        <p class="text-xs text-muted-foreground">{{ $member->joined_on?->format('j M Y') ?? '—' }} to {{ $member->left_on->format('j M Y') }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
