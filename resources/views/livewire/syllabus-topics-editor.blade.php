<div class="space-y-4">
    <april:card>
        <slot:title>Weekly topics</slot:title>
        <slot:description>Plan what is taught each week of the {{ strtolower(school_term('period', 'period')) }}. Students see these topics once the syllabus is published.</slot:description>
        <slot:content>
            @forelse ($topicsByWeek as $weekLabel => $topics)
                <section class="mb-4 last:mb-0" wire:key="week-{{ $weekLabel }}">
                    <h3 class="mb-2 text-sm font-semibold text-muted-foreground">{{ $weekLabel }}</h3>
                    <ul class="divide-y rounded-md border">
                        @foreach ($topics as $topic)
                            <li class="flex flex-col gap-2 p-3 sm:flex-row sm:items-start sm:justify-between" wire:key="topic-{{ $topic->id }}">
                                <div class="min-w-0 space-y-1">
                                    <p class="font-medium">{{ $topic->title }}</p>
                                    @if ($topic->objectives)
                                        <p class="text-sm text-muted-foreground"><span class="font-medium">Objectives:</span> {{ $topic->objectives }}</p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 gap-1">
                                    <april:button type="button" size="sm" variant="outline" wire:click="editTopic({{ $topic->id }})" aria-label="Edit {{ $topic->title }}">Edit</april:button>
                                    <april:button type="button" size="sm" variant="ghost" class="text-destructive" wire:click="deleteTopic({{ $topic->id }})" wire:confirm="Remove {{ $topic->title }} from this syllabus?" aria-label="Remove {{ $topic->title }}">Remove</april:button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @empty
                <p class="text-sm text-muted-foreground">No topics yet. Add the first topic below. A syllabus needs at least one topic before it can be published.</p>
            @endforelse
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>{{ $editingTopicId === null ? 'Add a topic' : 'Edit topic' }}</slot:title>
        <slot:content>
            <form wire:submit="saveTopic" class="grid gap-4 md:grid-cols-6">
                <div class="md:col-span-1">
                    <april:input-group id="topic-week" wire:model="week" type="number" min="1" max="60" label="Week" placeholder="1" />
                </div>
                <div class="md:col-span-5">
                    <april:input-group id="topic-title" wire:model="title" label="Topic *" placeholder="Eg: Linear equations in one variable" />
                </div>
                <div class="flex flex-col gap-2 md:col-span-6">
                    <april:label for="topic-objectives">Learning objectives</april:label>
                    <april:textarea id="topic-objectives" wire:model="objectives" rows="3" placeholder="By the end of the week, students should be able to…" />
                    <x-field-error name="objectives" />
                </div>
                <div class="flex flex-col gap-2 md:col-span-3">
                    <april:label for="topic-content">Content and activities</april:label>
                    <april:textarea id="topic-content" wire:model="content" rows="4" placeholder="Sub-topics, class activities, practice work" />
                    <x-field-error name="content" />
                </div>
                <div class="flex flex-col gap-2 md:col-span-3">
                    <april:label for="topic-resources">Resources</april:label>
                    <april:textarea id="topic-resources" wire:model="resources" rows="4" placeholder="Textbook chapters, materials, links" />
                    <x-field-error name="resources" />
                </div>
                <div class="flex gap-2 md:col-span-6">
                    <april:button type="submit" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveTopic">{{ $editingTopicId === null ? 'Add topic' : 'Save topic' }}</span>
                        <span wire:loading wire:target="saveTopic">Saving…</span>
                    </april:button>
                    @if ($editingTopicId !== null)
                        <april:button type="button" variant="outline" wire:click="cancelEdit">Cancel</april:button>
                    @endif
                </div>
            </form>
        </slot:content>
    </april:card>
</div>
