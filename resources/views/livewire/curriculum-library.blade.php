<div>
    <april:card>
        <slot:title>Curriculum library</slot:title>
        <slot:description>Weekly plans kept for reuse. Copy one into a syllabus draft from the draft's edit page.</slot:description>
        <slot:content>
            @if ($subjects->count() > 1)
                <div class="mb-4 flex flex-col gap-2 md:w-1/3">
                    <label for="library-subject" class="text-sm font-medium">Subject</label>
                    <select id="library-subject" wire:model.live="subjectId" class="h-10 rounded-md border border-input bg-background px-3 text-sm">
                        <option value="">All subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($outlines->isEmpty())
                <p class="text-sm text-muted-foreground">The library is empty. A reviewer saves a syllabus here from the syllabus page.</p>
            @else
                <ul class="divide-y rounded-md border">
                    @foreach ($outlines as $outline)
                        <li class="p-3" wire:key="outline-{{ $outline->id }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <h3 class="font-medium">{{ $outline->name }}</h3>
                                    <p class="text-sm text-muted-foreground">
                                        {{ $outline->subject->name }} · {{ $outline->academicLevel?->name ?? 'Any level' }}
                                        · {{ trans_choice(':count topic|:count topics', $outline->topics_count) }}{{ $outline->createdBy ? ' · saved by '.$outline->createdBy->name : '' }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <april:button type="button" size="sm" variant="outline" wire:click="toggle({{ $outline->id }})" aria-expanded="{{ $openOutlineId === $outline->id ? 'true' : 'false' }}">
                                        {{ $openOutlineId === $outline->id ? 'Hide topics' : 'Show topics' }}<span class="sr-only"> of {{ $outline->name }}</span>
                                    </april:button>
                                    @can('delete', $outline)
                                        <april:button type="button" size="sm" variant="ghost" class="text-destructive" wire:click="delete({{ $outline->id }})" wire:confirm="Remove {{ $outline->name }} from the library?">Remove<span class="sr-only"> {{ $outline->name }}</span></april:button>
                                    @endcan
                                </div>
                            </div>
                            @if ($openOutlineId === $outline->id)
                                <ol class="mt-3 space-y-2 text-sm">
                                    @foreach ($openTopics as $topic)
                                        <li wire:key="outline-topic-{{ $topic->id }}">
                                            <span class="text-muted-foreground">{{ $topic->week ? 'Week '.$topic->week : 'Unscheduled' }} ·</span>
                                            <span class="font-medium">{{ $topic->title }}</span>
                                            @if ($topic->objectives)
                                                <span class="block text-muted-foreground">{{ $topic->objectives }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </slot:content>
    </april:card>
</div>
