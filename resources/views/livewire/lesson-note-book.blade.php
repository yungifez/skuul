<div class="space-y-4">
    @php($fieldClass = 'flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring')
    <div class="flex flex-wrap items-end gap-4">
        @if (count($tracks) > 1)
            <div class="flex flex-col gap-2">
                <label for="lesson-note-track" class="text-sm font-medium">{{ school_term('section', 'Section') }}</label>
                <select id="lesson-note-track" wire:model.live="track" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                    @foreach ($tracks as $option)
                        <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="flex flex-col gap-2">
            <label for="lesson-note-status" class="text-sm font-medium">Status</label>
            <select id="lesson-note-status" wire:model.live="status" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                <option value="">All notes</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($canWrite)
        <april:card>
            <slot:title>{{ $editingId ? 'Edit lesson note' : 'Write a lesson note' }}</slot:title>
            <slot:description>Plan one week of lessons. Your head of department reviews the note after you send it.</slot:description>
            <slot:content>
                <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="lesson-note-week" class="text-sm font-medium">Week *</label>
                        <input id="lesson-note-week" type="number" min="1" max="60" wire:model="week" {{ field_error_bindings('week') }} class="{{ $fieldClass }} h-10">
                        <x-field-error name="week" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="lesson-note-topic" class="text-sm font-medium">Topic</label>
                        <select id="lesson-note-topic" wire:model="topicId" {{ field_error_bindings('topicId') }} class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                            <option value="">No planned topic</option>
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->week ? 'Week '.$topic->week.' · ' : '' }}{{ $topic->title }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="topicId" />
                    </div>
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label for="lesson-note-objectives" class="text-sm font-medium">Objectives *</label>
                        <textarea id="lesson-note-objectives" wire:model="objectives" rows="3" placeholder="By the end of the week, students can…" {{ field_error_bindings('objectives') }} class="{{ $fieldClass }}"></textarea>
                        <x-field-error name="objectives" />
                    </div>
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label for="lesson-note-activities" class="text-sm font-medium">Lesson activities *</label>
                        <textarea id="lesson-note-activities" wire:model="activities" rows="5" placeholder="Introduction, teaching steps, materials, and class work" {{ field_error_bindings('activities') }} class="{{ $fieldClass }}"></textarea>
                        <x-field-error name="activities" />
                    </div>
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label for="lesson-note-evaluation" class="text-sm font-medium">Evaluation</label>
                        <textarea id="lesson-note-evaluation" wire:model="evaluation" rows="2" placeholder="How you will check that students learned it (optional)" {{ field_error_bindings('evaluation') }} class="{{ $fieldClass }}"></textarea>
                        <x-field-error name="evaluation" />
                    </div>
                    <div class="flex gap-2 md:col-span-2">
                        <april:button type="submit" wire:loading.attr="disabled">{{ $editingId ? 'Save changes' : 'Save draft' }}</april:button>
                        @if ($editingId)
                            <april:button type="button" variant="outline" wire:click="cancel">Cancel</april:button>
                        @endif
                    </div>
                </form>
            </slot:content>
        </april:card>
    @endif

    <april:card>
        <slot:title>Lesson notes</slot:title>
        <slot:content>
            @if ($notes->isEmpty())
                <p class="text-sm text-muted-foreground">No lesson notes for this class yet.</p>
            @else
                <ul class="divide-y rounded-md border">
                    @foreach ($notes as $note)
                        <li class="space-y-3 p-4" wire:key="lesson-note-{{ $note->id }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <h3 class="font-semibold">Week {{ $note->week }}{{ $note->topic ? ' · '.$note->topic->title : '' }}</h3>
                                    <p class="text-sm text-muted-foreground">
                                        {{ $note->author?->name }}
                                        @if ($note->submitted_at)
                                            · sent {{ $note->submitted_at->format('M j, Y') }}
                                        @endif
                                        @if ($note->reviewedBy)
                                            · reviewed by {{ $note->reviewedBy->name }}
                                        @endif
                                    </p>
                                </div>
                                <span @class([
                                    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold',
                                    'border-transparent bg-primary text-primary-foreground' => $note->status === \App\Enums\LessonNoteStatus::Approved,
                                    'border-destructive/40 text-destructive' => $note->status === \App\Enums\LessonNoteStatus::Returned,
                                    'bg-muted text-muted-foreground' => in_array($note->status, [\App\Enums\LessonNoteStatus::Draft, \App\Enums\LessonNoteStatus::Submitted], true),
                                ])>{{ $note->status->label() }}</span>
                            </div>

                            @if ($note->status === \App\Enums\LessonNoteStatus::Returned && $note->review_note)
                                <p class="rounded-md border border-destructive/40 bg-destructive/5 p-3 text-sm">
                                    <span class="font-medium">Sent back for changes:</span> {{ $note->review_note }}
                                </p>
                            @endif

                            <dl class="grid gap-3 text-sm">
                                <div><dt class="font-medium">Objectives</dt><dd class="whitespace-pre-line text-muted-foreground">{{ $note->objectives }}</dd></div>
                                <div><dt class="font-medium">Lesson activities</dt><dd class="whitespace-pre-line text-muted-foreground">{{ $note->activities }}</dd></div>
                                @if ($note->evaluation)
                                    <div><dt class="font-medium">Evaluation</dt><dd class="whitespace-pre-line text-muted-foreground">{{ $note->evaluation }}</dd></div>
                                @endif
                            </dl>

                            <div class="flex flex-wrap items-center gap-2">
                                @can('update', $note)
                                    <april:button type="button" size="sm" variant="outline" wire:click="edit({{ $note->id }})">Edit<span class="sr-only"> the note for week {{ $note->week }}</span></april:button>
                                    <april:button type="button" size="sm" wire:click="submit({{ $note->id }})" wire:loading.attr="disabled">Send for review<span class="sr-only"> week {{ $note->week }}</span></april:button>
                                    <april:button type="button" size="sm" variant="ghost" class="text-destructive" wire:click="delete({{ $note->id }})" wire:confirm="Delete the lesson note for week {{ $note->week }}?">Delete<span class="sr-only"> the note for week {{ $note->week }}</span></april:button>
                                @endcan
                                @can('review', $note)
                                    <april:button type="button" size="sm" wire:click="approve({{ $note->id }})" wire:loading.attr="disabled">Approve<span class="sr-only"> the note for week {{ $note->week }}</span></april:button>
                                @endcan
                            </div>

                            @can('review', $note)
                                <form wire:submit="sendBack({{ $note->id }})" class="flex flex-col gap-2 md:w-1/2">
                                    <label for="review-note-{{ $note->id }}" class="text-sm font-medium">Send back for changes</label>
                                    <textarea id="review-note-{{ $note->id }}" wire:model="reviewNotes.{{ $note->id }}" rows="2" placeholder="What needs to change" {{ field_error_bindings('reviewNotes.'.$note->id) }} class="{{ $fieldClass }}"></textarea>
                                    <x-field-error name="reviewNotes.{{ $note->id }}" />
                                    <div><april:button type="submit" size="sm" variant="outline">Send back</april:button></div>
                                </form>
                            @endcan
                        </li>
                    @endforeach
                </ul>
            @endif
        </slot:content>
    </april:card>
</div>
