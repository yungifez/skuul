@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('incidents.index'), 'text' => 'Cases'],
    ['text' => $incident->reference, 'active'],
]])

@section('title', 'Case '.$incident->reference)
@section('page_heading', $incident->summary)

@section('page_actions')
    <april:button-link href="{{ route('incidents.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to cases
    </april:button-link>
@endsection

@php
    $outstanding = $incident->actions->filter(fn ($action) => $action->isOutstanding());
@endphp

@section('content')
    <div class="space-y-6">
        @if ($incident->is_restricted)
            <april:alert>
                <slot:icon><x-lucide-lock class="size-4" /></slot:icon>
                <slot:title>This case is restricted</slot:title>
                <slot:description>
                    Only the people who handle safeguarding, the person it is assigned to, and the person who
                    reported it can read this page. Do not repeat what it says outside that group.
                </slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>{{ $incident->reference }}</slot:title>
            <slot:description>{{ $incident->category->label() }} case, recorded by {{ $incident->reportedBy?->name ?? 'an unknown person' }}</slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">State</dt>
                            <dd class="text-lg font-semibold">{{ $incident->status->label() }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $incident->status->isOpen() ? 'This case still accepts work' : 'This case is finished' }}
                            </p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Happened</dt>
                            <dd class="text-lg font-semibold">{{ $incident->occurred_at->format('j M Y') }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $incident->occurred_at->format('H:i') }}
                                @if (filled($incident->location))
                                    · {{ $incident->location }}
                                @endif
                            </p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Handled by</dt>
                            <dd class="text-lg font-semibold">{{ $incident->assignedTo?->name ?? 'Nobody yet' }}</dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Still to do</dt>
                            <dd class="text-2xl font-semibold">{{ $outstanding->count() }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">Actions nobody has finished</p>
                        </div>
                    </dl>

                    @if (filled($incident->description))
                        <div class="rounded-lg border p-4">
                            <p class="text-sm text-muted-foreground">What happened</p>
                            <p class="mt-2 whitespace-pre-line text-sm">{{ $incident->description }}</p>
                        </div>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>People named in this case</slot:title>
            <slot:description>Each person appears once for each reason they appear.</slot:description>
            <slot:content>
                @if ($incident->participants->isEmpty())
                    <x-empty-state icon="lucide-users" title="Nobody is named"
                        description="The case records an event that names no learner." />
                @else
                    <april:data-table>
                        <slot:header>
                            <april:data-table-row>
                                <april:data-table-head>Person</april:data-table-head>
                                <april:data-table-head>Why they appear</april:data-table-head>
                                <april:data-table-head>Note</april:data-table-head>
                            </april:data-table-row>
                        </slot:header>
                        <slot:body>
                            @foreach ($incident->participants as $participant)
                                <april:data-table-row>
                                    <april:data-table-cell class="font-medium">
                                        {{ $participant->studentRecord?->user?->name ?? $participant->user?->name ?? 'Unnamed' }}
                                        @if ($participant->studentRecord !== null)
                                            <span class="block text-xs text-muted-foreground">{{ $participant->studentRecord->admission_number }}</span>
                                        @endif
                                    </april:data-table-cell>
                                    <april:data-table-cell>{{ $participant->role->label() }}</april:data-table-cell>
                                    <april:data-table-cell class="text-muted-foreground">{{ $participant->note ?? '—' }}</april:data-table-cell>
                                </april:data-table-row>
                            @endforeach
                        </slot:body>
                    </april:data-table>
                @endif
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>What the school is doing</slot:title>
            <slot:description>An action names one thing somebody has to do, and who has to do it.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($incident->actions->isEmpty())
                        <x-empty-state icon="lucide-list-checks" title="No actions yet"
                            description="Add what the school will do about this case." />
                    @else
                        <april:data-table>
                            <slot:header>
                                <april:data-table-row>
                                    <april:data-table-head>Action</april:data-table-head>
                                    <april:data-table-head>Who</april:data-table-head>
                                    <april:data-table-head>Due</april:data-table-head>
                                    <april:data-table-head class="text-right">State</april:data-table-head>
                                </april:data-table-row>
                            </slot:header>
                            <slot:body>
                                @foreach ($incident->actions as $action)
                                    <april:data-table-row>
                                        <april:data-table-cell class="font-medium">
                                            {{ $action->type }}
                                            <span class="block text-xs text-muted-foreground">{{ $action->description }}</span>
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-muted-foreground">{{ $action->assignedTo?->name ?? 'Nobody yet' }}</april:data-table-cell>
                                        <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                            {{ $action->due_on?->format('j M Y') ?? 'No date' }}
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-right">
                                            @if ($action->isOutstanding())
                                                @can('update', $incident)
                                                    <form method="POST" action="{{ route('incidents.actions.complete', [$incident, $action]) }}">
                                                        @csrf
                                                        <april:button type="submit" variant="outline" size="sm">
                                                            <x-lucide-check class="mr-1 size-4" />
                                                            Mark done
                                                        </april:button>
                                                    </form>
                                                @else
                                                    <span class="text-sm text-muted-foreground">Still to do</span>
                                                @endcan
                                            @else
                                                <span class="text-sm text-muted-foreground">Done {{ $action->completed_at->format('j M Y') }}</span>
                                            @endif
                                        </april:data-table-cell>
                                    </april:data-table-row>
                                @endforeach
                            </slot:body>
                        </april:data-table>
                    @endif

                    @can('update', $incident)
                        @if ($incident->status->isOpen())
                            <form method="POST" action="{{ route('incidents.actions.store', $incident) }}"
                                class="grid gap-4 border-t pt-6 lg:grid-cols-5 lg:items-end">
                                @csrf

                                <div class="flex flex-col gap-2">
                                    <april:label for="action-type">What kind</april:label>
                                    <april:input id="action-type" name="type" value="{{ old('type') }}" required
                                        placeholder="Meeting, detention, referral" />
                                    @error('type') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex flex-col gap-2 lg:col-span-2">
                                    <april:label for="action-description">What has to happen</april:label>
                                    <april:input id="action-description" name="description" value="{{ old('description') }}" required />
                                    @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex flex-col gap-2">
                                    <april:label for="action-assignee">Who</april:label>
                                    <april:native-select id="action-assignee" name="assigned_to">
                                        <option value="">Nobody yet</option>
                                        @foreach ($staff as $person)
                                            <option value="{{ $person->id }}" @selected(old('assigned_to') == $person->id)>{{ $person->name }}</option>
                                        @endforeach
                                    </april:native-select>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <april:label for="action-due">Due</april:label>
                                    <input type="date" id="action-due" name="due_on" value="{{ old('due_on') }}"
                                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                </div>

                                <april:button type="submit" class="lg:col-span-5 lg:justify-self-start">
                                    <x-lucide-plus class="mr-2 size-4" />
                                    Add this action
                                </april:button>
                            </form>
                        @else
                            <p class="border-t pt-6 text-sm text-muted-foreground">
                                This case is finished, so it takes no more actions. Move it back to Under review to work on it again.
                            </p>
                        @endif
                    @endcan
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Case notes</slot:title>
            <slot:description>Notes stay in the case history. Private notes are visible only to the people handling this case.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($notes->isEmpty())
                        <x-empty-state icon="lucide-notebook-pen" title="No notes yet"
                            description="Add a note when there is useful context to keep with this case." />
                    @else
                        <ol class="space-y-3">
                            @foreach ($notes as $note)
                                <li class="rounded-lg border p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="font-medium">{{ $note->writtenBy?->name ?? 'Unknown person' }}</p>
                                        <p class="text-sm text-muted-foreground">{{ $note->created_at->format('j M Y H:i') }}</p>
                                    </div>
                                    <p class="mt-2 whitespace-pre-line text-sm">{{ $note->body }}</p>
                                    @if ($note->is_restricted)
                                        <p class="mt-2 flex items-center gap-1 text-xs text-muted-foreground"><x-lucide-lock class="size-3" /> Private case note</p>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @endif

                    @can('update', $incident)
                        @if ($incident->status->isOpen())
                            <form method="POST" action="{{ route('incidents.notes.store', $incident) }}" class="space-y-4 border-t pt-6">
                                @csrf
                                <div class="flex flex-col gap-2">
                                    <april:label for="note-body">Add a note</april:label>
                                    <april:textarea id="note-body" name="body" rows="4" required>{{ old('body') }}</april:textarea>
                                    @error('body') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>
                                <label class="flex items-start gap-2 text-sm">
                                    <input type="hidden" name="is_restricted" value="0">
                                    <input type="checkbox" name="is_restricted" value="1" class="mt-0.5 size-4 rounded border-input" @checked(old('is_restricted', true))>
                                    <span><span class="font-medium">Keep this note private</span><span class="block text-muted-foreground">Only the case handler, reporter, and safeguarding readers can see it.</span></span>
                                </label>
                                @error('note') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                <april:button type="submit"><x-lucide-plus class="mr-2 size-4" />Add note</april:button>
                            </form>
                        @else
                            <p class="border-t pt-6 text-sm text-muted-foreground">This case is finished, so it takes no more notes.</p>
                        @endif
                    @endcan
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>How the case moved</slot:title>
            <slot:description>Every move is written down and stays. A correction is the next move, not an edit.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    <ol class="space-y-3">
                        <li class="rounded-lg border p-4">
                            <p class="font-medium">Reported</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $incident->created_at->format('j M Y') }}
                                · {{ $incident->reportedBy?->name ?? 'Unknown person' }}
                            </p>
                        </li>
                        @foreach ($incident->statusChanges as $change)
                            <li class="rounded-lg border p-4">
                                <p class="font-medium">{{ $change->from_status->label() }} → {{ $change->to_status->label() }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ $change->created_at->format('j M Y') }}
                                    · {{ $change->changedBy?->name ?? 'Unknown person' }}
                                    @if (filled($change->reason))
                                        · {{ $change->reason }}
                                    @endif
                                </p>
                            </li>
                        @endforeach
                    </ol>

                    @can('update', $incident)
                        @if ($nextStatuses !== [])
                            @if ($errors->has('status'))
                                <april:alert variant="destructive">
                                    <slot:title>The case did not move</slot:title>
                                    <slot:description>{{ $errors->first('status') }}</slot:description>
                                </april:alert>
                            @endif

                            <form method="POST" action="{{ route('incidents.status.update', $incident) }}"
                                class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                                @csrf
                                @method('PUT')

                                <div class="flex flex-col gap-2">
                                    <april:label for="status">Move the case to</april:label>
                                    <april:native-select id="status" name="status" required>
                                        @foreach ($nextStatuses as $status)
                                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                        @endforeach
                                    </april:native-select>
                                </div>

                                <div class="flex flex-col gap-2 lg:col-span-2">
                                    <april:label for="status-reason">Why</april:label>
                                    <april:input id="status-reason" name="reason" value="{{ old('reason') }}" placeholder="Optional" />
                                </div>

                                <april:button type="submit">
                                    <x-lucide-arrow-right class="mr-2 size-4" />
                                    Move the case
                                </april:button>
                            </form>
                        @else
                            <p class="border-t pt-6 text-sm text-muted-foreground">A closed case cannot move again.</p>
                        @endif
                    @endcan
                </div>
            </slot:content>
        </april:card>
    </div>
@endsection
