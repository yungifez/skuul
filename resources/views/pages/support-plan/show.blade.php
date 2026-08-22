@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('support-plans.index'), 'text' => 'Support plans'],
    ['text' => $plan->title, 'active'],
]])

@section('title', $plan->title)
@section('page_heading', $plan->title)

@section('page_actions')
    <april:button-link href="{{ route('support-plans.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to plans
    </april:button-link>
@endsection

@php
    $outstanding = $plan->actions->filter(fn ($action) => $action->completed_at === null);
    $isDue = $plan->status->isOpen() && $plan->review_on !== null && $plan->review_on->isPast();
@endphp

@section('content')
    <div class="space-y-6">
        @if ($plan->is_confidential)
            <april:alert>
                <slot:icon><x-lucide-lock class="size-4" /></slot:icon>
                <slot:title>This plan is confidential</slot:title>
                <slot:description>
                    Only the people who run it, the person it is assigned to, and the person who wrote it can read
                    this page. Do not repeat what it says outside that group.
                </slot:description>
            </april:alert>
        @endif

        @if ($isDue)
            <april:alert variant="destructive">
                <slot:title>This plan is due for review</slot:title>
                <slot:description>
                    Somebody agreed to look at it again on {{ $plan->review_on->format('j M Y') }}. Add a note, or
                    move the plan on.
                </slot:description>
            </april:alert>
        @endif

        <april:card>
            <slot:title>{{ $plan->studentRecord->user?->name ?? $plan->studentRecord->admission_number }}</slot:title>
            <slot:description>
                {{ $plan->studentRecord->admission_number }} ·
                {{ $plan->category->label() }} plan written by {{ $plan->createdBy?->name ?? 'an unknown person' }}
            </slot:description>
            <slot:content>
                <div class="space-y-6">
                    <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">State</dt>
                            <dd class="text-lg font-semibold">{{ $plan->status->label() }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $plan->status->isOpen() ? 'This plan still accepts work' : 'This plan is finished' }}
                            </p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Runs from</dt>
                            <dd class="text-lg font-semibold">{{ $plan->starts_on?->format('j M Y') ?? 'No date' }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">
                                {{ $plan->ends_on === null ? 'No end date' : 'Ended '.$plan->ends_on->format('j M Y') }}
                            </p>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Review</dt>
                            <dd class="text-lg font-semibold">{{ $plan->review_on?->format('j M Y') ?? 'No date' }}</dd>
                        </div>
                        <div class="rounded-lg border p-4">
                            <dt class="text-sm text-muted-foreground">Still to do</dt>
                            <dd class="text-2xl font-semibold">{{ $outstanding->count() }}</dd>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Run by {{ $plan->assignedTo?->name ?? 'nobody yet' }}
                            </p>
                        </div>
                    </dl>

                    @if (filled($plan->summary))
                        <div class="rounded-lg border p-4">
                            <p class="text-sm text-muted-foreground">What the plan says</p>
                            <p class="mt-2 whitespace-pre-line text-sm">{{ $plan->summary }}</p>
                        </div>
                    @endif
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Steps</slot:title>
            <slot:description>A step names one thing somebody agreed to do, and who has to do it.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($plan->actions->isEmpty())
                        <x-empty-state icon="lucide-list-checks" title="No steps yet"
                            description="Add what the school agreed to do for this child." />
                    @else
                        <april:data-table>
                            <slot:header>
                                <april:data-table-row>
                                    <april:data-table-head>Step</april:data-table-head>
                                    <april:data-table-head>Who</april:data-table-head>
                                    <april:data-table-head>Due</april:data-table-head>
                                    <april:data-table-head class="text-right">State</april:data-table-head>
                                </april:data-table-row>
                            </slot:header>
                            <slot:body>
                                @foreach ($plan->actions as $action)
                                    <april:data-table-row>
                                        <april:data-table-cell class="font-medium">{{ $action->description }}</april:data-table-cell>
                                        <april:data-table-cell class="text-muted-foreground">{{ $action->assignedTo?->name ?? 'Nobody yet' }}</april:data-table-cell>
                                        <april:data-table-cell class="whitespace-nowrap text-muted-foreground">
                                            {{ $action->due_on?->format('j M Y') ?? 'No date' }}
                                        </april:data-table-cell>
                                        <april:data-table-cell class="text-right">
                                            @if ($action->completed_at === null)
                                                @can('update', $plan)
                                                    <form method="POST" action="{{ route('support-plans.actions.complete', [$plan, $action]) }}">
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

                    @can('update', $plan)
                        @if ($plan->status->isOpen())
                            <form method="POST" action="{{ route('support-plans.actions.store', $plan) }}"
                                class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                                @csrf

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
                                            <option value="{{ $person->id }}">{{ $person->name }}</option>
                                        @endforeach
                                    </april:native-select>
                                </div>

                                <div class="flex flex-col gap-2">
                                    <april:label for="action-due">Due</april:label>
                                    <input type="date" id="action-due" name="due_on" value="{{ old('due_on') }}"
                                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                </div>

                                <april:button type="submit" class="lg:col-span-4 lg:justify-self-start">
                                    <x-lucide-plus class="mr-2 size-4" />
                                    Add this step
                                </april:button>
                            </form>
                        @else
                            <p class="border-t pt-6 text-sm text-muted-foreground">
                                This plan is finished, so it takes no more steps. Make it active again to work on it.
                            </p>
                        @endif
                    @endcan
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>Notes</slot:title>
            <slot:description>Write down how the plan is going, so the next person reads the same story.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    @if ($plan->notes->isEmpty())
                        <x-empty-state icon="lucide-notebook-pen" title="No notes yet"
                            description="A note records what happened between one review and the next." />
                    @else
                        <ol class="space-y-3">
                            @foreach ($plan->notes as $note)
                                <li class="rounded-lg border p-4">
                                    <p class="whitespace-pre-line text-sm">{{ $note->body }}</p>
                                    <p class="mt-2 text-xs text-muted-foreground">
                                        {{ $note->created_at->format('j M Y') }} · {{ $note->writtenBy?->name ?? 'Unknown person' }}
                                    </p>
                                </li>
                            @endforeach
                        </ol>
                    @endif

                    @can('update', $plan)
                        @if ($plan->status->isOpen())
                            <form method="POST" action="{{ route('support-plans.notes.store', $plan) }}" class="space-y-4 border-t pt-6">
                                @csrf
                                <div class="flex flex-col gap-2">
                                    <april:label for="note-body">Add a note</april:label>
                                    <textarea id="note-body" name="body" rows="3" required
                                        class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">{{ old('body') }}</textarea>
                                    @error('body') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                                </div>
                                <april:button type="submit">
                                    <x-lucide-plus class="mr-2 size-4" />
                                    Add this note
                                </april:button>
                            </form>
                        @endif
                    @endcan
                </div>
            </slot:content>
        </april:card>

        <april:card>
            <slot:title>How the plan moved</slot:title>
            <slot:description>Every move is written down and stays. A correction is the next move, not an edit.</slot:description>
            <slot:content>
                <div class="space-y-6">
                    <ol class="space-y-3">
                        <li class="rounded-lg border p-4">
                            <p class="font-medium">Written</p>
                            <p class="text-sm text-muted-foreground">
                                {{ $plan->created_at->format('j M Y') }} · {{ $plan->createdBy?->name ?? 'Unknown person' }}
                            </p>
                        </li>
                        @foreach ($plan->statusChanges as $change)
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

                    @can('update', $plan)
                        @if ($nextStatuses !== [])
                            @if ($errors->has('status'))
                                <april:alert variant="destructive">
                                    <slot:title>The plan did not move</slot:title>
                                    <slot:description>{{ $errors->first('status') }}</slot:description>
                                </april:alert>
                            @endif

                            <form method="POST" action="{{ route('support-plans.status.update', $plan) }}"
                                class="grid gap-4 border-t pt-6 lg:grid-cols-4 lg:items-end">
                                @csrf
                                @method('PUT')

                                <div class="flex flex-col gap-2">
                                    <april:label for="status">Move the plan to</april:label>
                                    <april:native-select id="status" name="status" required>
                                        @foreach ($nextStatuses as $status)
                                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                        @endforeach
                                    </april:native-select>
                                </div>

                                <div class="flex flex-col gap-2 lg:col-span-2">
                                    <april:label for="status-reason">Why</april:label>
                                    <april:input id="status-reason" name="reason" placeholder="Optional" />
                                </div>

                                <april:button type="submit">
                                    <x-lucide-arrow-right class="mr-2 size-4" />
                                    Move the plan
                                </april:button>
                            </form>
                        @else
                            <p class="border-t pt-6 text-sm text-muted-foreground">A cancelled plan cannot move again.</p>
                        @endif
                    @endcan
                </div>
            </slot:content>
        </april:card>
    </div>
@endsection
