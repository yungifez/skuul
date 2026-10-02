<div class="mx-auto flex w-full max-w-4xl flex-col gap-10">
    @php
        $outstanding = $incident->actions->filter(fn ($action) => $action->isOutstanding());
        $isOpen = $incident->status->isOpen();
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <section aria-label="Case summary" class="flex flex-col gap-6">
        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
            <span class="font-medium text-foreground">{{ $incident->reference }}</span>
            <span>· {{ $incident->category->label() }}</span>
            <span>· Recorded by {{ $incident->reportedBy?->name ?? 'an unknown person' }}</span>
            @if ($incident->is_restricted)
                <span class="inline-flex items-center gap-1 text-foreground"><x-lucide-lock class="size-3.5" aria-hidden="true" /> Restricted</span>
            @endif
        </p>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 border-y py-4 sm:grid-cols-4">
            <div>
                <dt class="text-sm text-muted-foreground">State</dt>
                <dd class="font-medium" id="incident-status">{{ $incident->status->label() }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Happened</dt>
                <dd class="font-medium">{{ $incident->occurred_at->format('j M Y, H:i') }}</dd>
                @if (filled($incident->location))
                    <dd class="text-sm text-muted-foreground">{{ $incident->location }}</dd>
                @endif
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Handled by</dt>
                <dd @class(['font-medium', 'text-muted-foreground' => $incident->assignedTo === null])>{{ $incident->assignedTo?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Still to do</dt>
                <dd class="font-medium">{{ $outstanding->count() }}</dd>
            </div>
        </dl>

        @if (filled($incident->description))
            <p class="text-sm whitespace-pre-line">{{ $incident->description }}</p>
        @endif

        @if ($canUpdate && $nextStatuses !== [])
            <form wire:submit="changeStatus" class="flex flex-col gap-3 sm:flex-row sm:items-start" aria-label="Move the case">
                <div class="sm:w-48">
                    <label for="status" class="sr-only">Move the case to</label>
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
                <april:button type="submit" class="h-11 w-full sm:w-auto" wire:loading.attr="disabled">Move the case</april:button>
            </form>
            <x-field-error name="nextStatus" />
            <x-field-error name="statusReason" />
        @endif
    </section>

    <section aria-labelledby="people-heading" class="flex flex-col gap-3">
        <h2 id="people-heading" class="text-base font-semibold">People</h2>
        @if ($incident->participants->isEmpty())
            <p class="text-sm text-muted-foreground">Nobody is named.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($incident->participants as $participant)
                    <li wire:key="participant-{{ $participant->id }}" class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 py-3 text-sm">
                        <span class="font-medium">
                            {{ $participant->studentRecord?->user?->name ?? $participant->user?->name ?? 'Unnamed' }}
                            @if ($participant->studentRecord !== null)
                                <span class="ml-1 font-normal text-muted-foreground">{{ $participant->studentRecord->admission_number }}</span>
                            @endif
                        </span>
                        <span class="text-muted-foreground">{{ $participant->role->label() }}@if (filled($participant->note)) · {{ $participant->note }}@endif</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section aria-labelledby="actions-heading" class="flex flex-col gap-3">
        <h2 id="actions-heading" class="text-base font-semibold">Actions</h2>
        @if ($incident->actions->isEmpty())
            <p class="text-sm text-muted-foreground">No actions yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($incident->actions as $action)
                    <li wire:key="action-{{ $action->id }}" class="flex items-center justify-between gap-4 py-3">
                        <div class="min-w-0 text-sm">
                            <p @class(['font-medium', 'text-muted-foreground line-through' => !$action->isOutstanding()])>{{ $action->type }}</p>
                            <p class="text-muted-foreground">{{ $action->description }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $action->assignedTo?->name ?? 'Nobody yet' }}
                                · {{ $action->due_on?->format('j M Y') ?? 'No date' }}
                                @if (!$action->isOutstanding())
                                    · Done {{ school_time($action->completed_at)?->format('j M Y') }}
                                @endif
                            </p>
                        </div>
                        @if ($action->isOutstanding() && $canUpdate)
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
            <form wire:submit="addAction" class="flex flex-col gap-3 pt-2" aria-label="Add an action">
                <div class="grid gap-3 sm:grid-cols-[10rem_1fr]">
                    <div>
                        <label for="action-type" class="sr-only">What kind</label>
                        <input id="action-type" wire:model="actionType" maxlength="100" placeholder="Meeting, referral…" class="{{ $controlClasses }}" {{ field_error_bindings('actionType') }}>
                        <x-field-error name="actionType" class="mt-1" />
                    </div>
                    <div>
                        <label for="action-description" class="sr-only">What has to happen</label>
                        <input id="action-description" wire:model="actionDescription" maxlength="1000" placeholder="What has to happen" class="{{ $controlClasses }}" {{ field_error_bindings('actionDescription') }}>
                        <x-field-error name="actionDescription" class="mt-1" />
                    </div>
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
                    <april:button type="submit" variant="outline" class="h-11 w-full sm:w-auto" wire:loading.attr="disabled">Add action</april:button>
                </div>
            </form>
        @endif
    </section>

    <section aria-labelledby="notes-heading" class="flex flex-col gap-3">
        <h2 id="notes-heading" class="text-base font-semibold">Notes</h2>
        @if ($notes->isEmpty())
            <p class="text-sm text-muted-foreground">No notes yet.</p>
        @else
            <ol class="divide-y border-y">
                @foreach ($notes as $note)
                    <li wire:key="note-{{ $note->id }}" class="py-3 text-sm">
                        <p class="flex flex-wrap items-center gap-x-2 text-muted-foreground">
                            <span class="font-medium text-foreground">{{ $note->writtenBy?->name ?? 'Unknown person' }}</span>
                            <span>{{ school_time($note->created_at)?->format('j M Y H:i') }}</span>
                            @if ($note->is_restricted)
                                <span class="inline-flex items-center gap-1 text-xs"><x-lucide-lock class="size-3" aria-hidden="true" /> Private</span>
                            @endif
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
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <label for="note-private" class="flex min-h-11 cursor-pointer items-center gap-2 text-sm select-none">
                        <input type="checkbox" id="note-private" wire:model="noteIsRestricted" class="size-4 rounded border-input">
                        Private to the case handlers
                    </label>
                    <april:button type="submit" variant="outline" class="h-11 w-full sm:w-auto" wire:loading.attr="disabled">Add note</april:button>
                </div>
            </form>
        @endif
    </section>

    <section aria-labelledby="history-heading" class="flex flex-col gap-3">
        <h2 id="history-heading" class="text-base font-semibold">History</h2>
        <ol class="flex flex-col gap-3 border-l pl-4 text-sm">
            <li>
                <p class="font-medium">Reported</p>
                <p class="text-muted-foreground">{{ school_time($incident->created_at)?->format('j M Y') }} · {{ $incident->reportedBy?->name ?? 'Unknown person' }}</p>
            </li>
            @foreach ($incident->statusChanges as $change)
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
