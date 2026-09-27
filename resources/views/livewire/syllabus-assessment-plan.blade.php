<div>
    <april:card>
        <slot:title>Assessment plan</slot:title>
        <slot:description>How the course is assessed, from the gradebook, and the topics each assessment tests.</slot:description>
        <slot:content>
            @if ($plan === [])
                <p class="text-sm text-muted-foreground">
                    No assessments are set up for this course yet.
                    @if ($canOpenGradebook)
                        <a class="font-medium underline" href="{{ route('course-offerings.gradebook.show', $syllabus->courseOffering) }}">Open the gradebook</a> to add them.
                    @endif
                </p>
            @else
                <div class="space-y-4">
                    @foreach ($plan as $group)
                        <section class="rounded-md border p-3" wire:key="assessment-group-{{ $loop->index }}">
                            <h3 class="mb-2 flex flex-wrap items-baseline justify-between gap-2 text-sm font-semibold">
                                <span>{{ $group['name'] }}</span>
                                <span class="font-normal text-muted-foreground">{{ $group['share'] }}% of the final grade · {{ strtolower($group['aggregation']->label()) }}</span>
                            </h3>
                            <ul class="divide-y">
                                @foreach ($group['items'] as $row)
                                    @php($item = $row['item'])
                                    <li class="py-2" wire:key="assessment-item-{{ $item->id }}">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <p class="font-medium">
                                                {{ $item->name }}
                                                <span class="text-sm font-normal text-muted-foreground">
                                                    · {{ $row['share'] === null ? 'best result counts' : $row['share'].'%' }}
                                                    @if ($item->due_on)
                                                        · due {{ $item->due_on->format('M j') }}{{ $row['week'] ? ' (week '.$row['week'].')' : '' }}
                                                    @endif
                                                </span>
                                            </p>
                                            @if ($canTag && $editingItemId !== $item->id)
                                                <april:button type="button" size="sm" variant="outline" wire:click="edit({{ $item->id }})">Choose topics<span class="sr-only"> for {{ $item->name }}</span></april:button>
                                            @endif
                                        </div>

                                        @if ($editingItemId === $item->id)
                                            <form wire:submit="saveTopics" class="mt-2 space-y-2">
                                                <fieldset>
                                                    <legend class="mb-1 text-sm font-medium">Topics that {{ $item->name }} tests</legend>
                                                    <div class="grid gap-1 sm:grid-cols-2">
                                                        @foreach ($topics as $topic)
                                                            <label class="flex items-center gap-2 text-sm" wire:key="assessment-topic-{{ $item->id }}-{{ $topic->id }}">
                                                                <input type="checkbox" value="{{ $topic->id }}" wire:model="topicIds" class="rounded border-input">
                                                                {{ $topic->week ? 'Week '.$topic->week.' · ' : '' }}{{ $topic->title }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </fieldset>
                                                <div class="flex gap-2">
                                                    <april:button type="submit" size="sm">Save topics</april:button>
                                                    <april:button type="button" size="sm" variant="outline" wire:click="cancel">Cancel</april:button>
                                                </div>
                                            </form>
                                        @elseif ($item->syllabusTopics->isNotEmpty())
                                            <p class="mt-1 text-sm text-muted-foreground">Tests: {{ $item->syllabusTopics->pluck('title')->join(', ') }}</p>
                                        @endif

                                        @if ($row['early_topics'] !== [])
                                            <p class="mt-1 text-sm text-destructive">Due before these topics are taught: {{ implode(', ', $row['early_topics']) }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>

                @if ($untested->isNotEmpty() && $canTag)
                    <p class="mt-4 text-sm" id="untested-topics">
                        <span class="font-medium">Not tested by any assessment:</span>
                        {{ $untested->pluck('title')->join(', ') }}
                    </p>
                @endif
            @endif
        </slot:content>
    </april:card>
</div>
