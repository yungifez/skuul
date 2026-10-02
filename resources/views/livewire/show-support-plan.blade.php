<div class="mx-auto flex w-full max-w-4xl flex-col gap-10">
    @php
        $outstanding = $plan->actions->filter(fn ($action) => $action->completed_at === null);
        $isOpen = $plan->status->isOpen();
        $isDue = $isOpen && $plan->review_on !== null && $plan->review_on->isPast();
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <section aria-label="Plan summary" class="flex flex-col gap-6">
        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
            <span class="font-medium text-foreground">{{ $plan->studentRecord->user?->name ?? $plan->studentRecord->admission_number }}</span>
            <span>· {{ $plan->studentRecord->admission_number }}</span>
            <span>· {{ $plan->category->label() }}</span>
            <span>· Written by {{ $plan->createdBy?->name ?? 'an unknown person' }}</span>
            @if ($plan->is_confidential)
                <span class="inline-flex items-center gap-1 text-foreground"><x-lucide-lock class="size-3.5" aria-hidden="true" /> Confidential</span>
            @endif
        </p>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4">
            <div>
                <dt class="text-sm text-muted-foreground">State</dt>
                <dd class="font-medium" id="plan-status">{{ $plan->status->label() }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Runs</dt>
                <dd @class(['font-medium', 'text-muted-foreground' => $plan->starts_on === null])>{{ $plan->starts_on?->format('j M Y') ?? '—' }}</dd>
                @if ($plan->ends_on !== null)
                    <dd class="text-sm text-muted-foreground">to {{ $plan->ends_on->format('j M Y') }}</dd>
                @endif
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Review</dt>
                <dd @class(['font-medium', 'text-destructive' => $isDue, 'text-muted-foreground' => $plan->review_on === null])>
                    {{ $plan->review_on?->format('j M Y') ?? '—' }}@if ($isDue) · due @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Run by</dt>
                <dd @class(['font-medium', 'text-muted-foreground' => $plan->assignedTo === null])>{{ $plan->assignedTo?->name ?? '—' }}</dd>
            </div>
        </dl>

        @if (filled($plan->summary))
            <p class="text-sm whitespace-pre-line">{{ $plan->summary }}</p>
        @endif

        @if ($canUpdate && $nextStatuses !== [])
            <form wire:submit="changeStatus" class="flex flex-col gap-3 sm:flex-row sm:items-start" aria-label="Move the plan">
                <div class="sm:w-48">
                    <label for="status" class="sr-only">Move the plan to</label>
                    <select id="status" wire:model="nextStatus" class="{{ $controlClasses }}" {{ field_error_bindings('nextStatus') }}>
                        @foreach ($nextStatuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0 flex-1">
                    <label for="status-reason" class="sr-only">Why</label>
                    <input id="status-reason" wire:model="statusReason" maxlength="500" placeholder="Why (optional)" class="{{ $controlClasses }}" {{ field_error_bindings('statusReason') }}>
                </div>
                <april:button type="submit" class="h-11 w-full select-none sm:w-auto" wire:loading.attr="disabled" wire:target="changeStatus">Move the plan</april:button>
            </form>
            <x-field-error name="nextStatus" />
            <x-field-error name="statusReason" />
        @endif
    </section>

    <section aria-labelledby="steps-heading" class="flex flex-col gap-3">
        <h2 id="steps-heading" class="text-base font-semibold">Steps <span class="font-normal text-muted-foreground">· {{ $outstanding->count() }} to do</span></h2>
        @if ($plan->actions->isEmpty())
            <p class="text-sm text-muted-foreground">No steps yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($plan->actions as $action)
                    <li wire:key="step-{{ $action->id }}" class="flex items-center justify-between gap-4 py-3">
                        <div class="min-w-0 text-sm">
                            <p @class(['font-medium', 'text-muted-foreground line-through' => $action->completed_at !== null])>{{ $action->description }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $action->assignedTo?->name ?? 'Nobody yet' }}
                                · {{ $action->due_on?->format('j M Y') ?? 'No date' }}
                                @if ($action->completed_at !== null)
                                    · Done {{ school_time($action->completed_at)?->format('j M Y') }}
                                @endif
                            </p>
                        </div>
                        @if ($action->completed_at === null && $canUpdate)
                            <button type="button" wire:click="completeAction({{ $action->id }})" wire:loading.attr="disabled"
                                class="inline-flex h-11 shrink-0 items-center gap-1 rounded-md border px-3 text-sm font-medium select-none hover:bg-muted">
                                <x-lucide-check class="size-4" aria-hidden="true" />
                                Mark done
                            </button>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($canUpdate && $isOpen)
            <form wire:submit="addAction" class="flex flex-col gap-3 pt-2" aria-label="Add a step">
                <div>
                    <label for="action-description" class="sr-only">What has to happen</label>
                    <input id="action-description" wire:model="actionDescription" maxlength="1000" placeholder="What has to happen" class="{{ $controlClasses }}" {{ field_error_bindings('actionDescription') }}>
                    <x-field-error name="actionDescription" class="mt-1" />
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div class="min-w-0 flex-1">
                        <label for="action-assignee" class="sr-only">Who</label>
                        <select id="action-assignee" wire:model="actionAssigneeId" class="{{ $controlClasses }}" {{ field_error_bindings('actionAssigneeId') }}>
                            <option value="">Nobody yet</option>
                            @foreach ($staff as $person)
                                <option value="{{ $person->id }}">{{ $person->name }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="actionAssigneeId" class="mt-1" />
                    </div>
                    <div class="sm:w-44">
                        <label for="action-due" class="sr-only">Due</label>
                        <input type="date" id="action-due" wire:model="actionDueOn" class="{{ $controlClasses }}" {{ field_error_bindings('actionDueOn') }}>
                        <x-field-error name="actionDueOn" class="mt-1" />
                    </div>
                    <april:button type="submit" variant="outline" class="h-11 w-full select-none sm:w-auto" wire:loading.attr="disabled" wire:target="addAction">Add step</april:button>
                </div>
            </form>
        @endif
    </section>

    <section aria-labelledby="notes-heading" class="flex flex-col gap-3">
        <h2 id="notes-heading" class="text-base font-semibold">Notes</h2>
        @if ($plan->notes->isEmpty())
            <p class="text-sm text-muted-foreground">No notes yet.</p>
        @else
            <ol class="divide-y border-y">
                @foreach ($plan->notes as $note)
                    <li wire:key="note-{{ $note->id }}" class="py-3 text-sm">
                        <p class="flex flex-wrap items-center gap-x-2 text-muted-foreground">
                            <span class="font-medium text-foreground">{{ $note->writtenBy?->name ?? 'Unknown person' }}</span>
                            <span>{{ school_time($note->created_at)?->format('j M Y') }}</span>
                        </p>
                        <p class="mt-1 whitespace-pre-line">{{ $note->body }}</p>
                    </li>
                @endforeach
            </ol>
        @endif

        @if ($canUpdate && $isOpen)
            <form wire:submit="addNote" class="flex flex-col gap-3 pt-2" aria-label="Add a note">
                <label for="note-body" class="sr-only">Note</label>
                <textarea id="note-body" wire:model="noteBody" rows="3" maxlength="5000" placeholder="Add a note"
                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" {{ field_error_bindings('noteBody') }}></textarea>
                <x-field-error name="noteBody" />
                <div class="flex sm:justify-end">
                    <april:button type="submit" variant="outline" class="h-11 w-full select-none sm:w-auto" wire:loading.attr="disabled" wire:target="addNote">Add note</april:button>
                </div>
            </form>
        @endif
    </section>

    <section aria-labelledby="history-heading" class="flex flex-col gap-3">
        <h2 id="history-heading" class="text-base font-semibold">History</h2>
        <ol class="flex flex-col gap-3 border-l pl-4 text-sm">
            <li>
                <p class="font-medium">Written</p>
                <p class="text-muted-foreground">{{ school_time($plan->created_at)?->format('j M Y') }} · {{ $plan->createdBy?->name ?? 'Unknown person' }}</p>
            </li>
            @foreach ($plan->statusChanges as $change)
                <li wire:key="change-{{ $change->id }}">
                    <p class="font-medium">{{ $change->from_status->label() }} → {{ $change->to_status->label() }}</p>
                    <p class="text-muted-foreground">
                        {{ school_time($change->created_at)?->format('j M Y') }} · {{ $change->changedBy?->name ?? 'Unknown person' }}
                        @if (filled($change->reason))
                            · {{ $change->reason }}
                        @endif
                    </p>
                </li>
            @endforeach
        </ol>
    </section>
</div>
