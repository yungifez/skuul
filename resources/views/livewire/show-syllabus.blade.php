<div class="space-y-4">
    <april:card>
        <slot:title>{{ $syllabus->name }}</slot:title>
        <slot:content>
            <div class="mb-4 flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                <span @class([
                    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold',
                    'border-transparent bg-primary text-primary-foreground' => $syllabus->status === \App\Enums\SyllabusStatus::Published,
                    'bg-muted text-muted-foreground' => $syllabus->status !== \App\Enums\SyllabusStatus::Published,
                ])>{{ $syllabus->status->label() }}</span>
                <span>Revision {{ $syllabus->revision }}</span>
                @if ($syllabus->published_at)
                    <span>· Published {{ $syllabus->published_at->format('M j, Y') }}{{ $syllabus->publishedBy ? ' by '.$syllabus->publishedBy->name : '' }}</span>
                @endif
            </div>

            <dl class="mb-5 grid gap-3 text-sm sm:grid-cols-3">
                <div><dt class="text-muted-foreground">Subject</dt><dd class="font-medium">{{ $syllabus->courseOffering->subject->name }}</dd></div>
                <div><dt class="text-muted-foreground">{{ school_term('class_level', 'Class') }}</dt><dd class="font-medium">{{ $syllabus->courseOffering->academicLevel->name }}</dd></div>
                <div><dt class="text-muted-foreground">{{ school_term('period', 'Academic period') }}</dt><dd class="font-medium">{{ $syllabus->courseOffering->academicPeriod->label ?? $syllabus->courseOffering->academicPeriod->name }}</dd></div>
            </dl>

            @if ($syllabus->description)
                <p class="mb-4 whitespace-pre-line">{{ $syllabus->description }}</p>
            @endif

            @if ($syllabus->status === \App\Enums\SyllabusStatus::Superseded)
                <p class="mb-4 rounded-md border bg-muted/40 p-3 text-sm">
                    A newer revision replaced this one.
                    @if ($replacement)
                        <a class="font-medium underline" href="{{ route('syllabi.show', $replacement) }}">Open revision {{ $replacement->revision }}</a>.
                    @endif
                </p>
            @endif

            @if ($syllabus->status === \App\Enums\SyllabusStatus::Submitted)
                <p class="mb-4 rounded-md border bg-muted/40 p-3 text-sm">
                    Waiting for review{{ $syllabus->submittedBy ? ' · sent by '.$syllabus->submittedBy->name : '' }}{{ $syllabus->submitted_at ? ' on '.$syllabus->submitted_at->format('M j, Y') : '' }}.
                </p>
            @elseif ($syllabus->status === \App\Enums\SyllabusStatus::Draft && $syllabus->review_note)
                <p class="mb-4 rounded-md border border-destructive/40 bg-destructive/5 p-3 text-sm">
                    <span class="font-medium">Sent back for changes:</span> {{ $syllabus->review_note }}
                </p>
            @endif

            @if ($syllabus->change_note)
                <p class="mb-4 text-sm"><span class="font-medium">Why this revision:</span> {{ $syllabus->change_note }}</p>
            @endif

            <div class="flex flex-wrap items-start gap-2">
                @if ($syllabus->file)
                    <april:button type="button" variant="outline" wire:click="download">
                        <x-lucide-download class="mr-2 size-4" />
                        Download PDF
                    </april:button>
                @endif

                @can('viewAny', [\App\Models\LessonNote::class, $syllabus])
                    <april:button-link href="{{ route('syllabi.lesson-notes', $syllabus) }}" variant="outline">Lesson notes</april:button-link>
                @endcan

                @can('update', $syllabus)
                    @if ($syllabus->status === \App\Enums\SyllabusStatus::Draft)
                        <april:button-link href="{{ route('syllabi.edit', $syllabus) }}" variant="outline">Edit draft</april:button-link>
                    @elseif ($syllabus->status === \App\Enums\SyllabusStatus::Published && $openRevision)
                        <april:button-link href="{{ route('syllabi.edit', $openRevision) }}" variant="outline">Continue draft revision {{ $openRevision->revision }}</april:button-link>
                    @endif
                @endcan
            </div>

            @if (in_array($syllabus->status, [\App\Enums\SyllabusStatus::Draft, \App\Enums\SyllabusStatus::Submitted, \App\Enums\SyllabusStatus::Published], true))
                <div class="mt-4">
                    @livewire('syllabus-workflow-control', ['syllabus' => $syllabus], key('workflow-'.$syllabus->id))
                </div>
            @endif
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Weekly plan</slot:title>
        <slot:content>
            @if ($currentWeek)
                <p class="mb-3 text-sm text-muted-foreground">This is week {{ $currentWeek }} of the {{ strtolower(school_term('period', 'period')) }}.</p>
            @endif
            @forelse ($topicsByWeek as $week => $topics)
                @php($isCurrentWeek = (string) $currentWeek === $week)
                <section @class(['mb-4 rounded-md border p-3 last:mb-0', 'border-primary bg-primary/5' => $isCurrentWeek])>
                    <h3 class="mb-2 text-sm font-semibold">
                        {{ $week === 'Unscheduled' ? 'Unscheduled' : 'Week '.$week }}
                        @if ($isCurrentWeek)
                            <span class="ml-1 text-xs font-medium text-primary">This week</span>
                        @endif
                    </h3>
                    <ul class="space-y-3">
                        @foreach ($topics as $topic)
                            <li>
                                <p class="font-medium">
                                    {{ $topic->title }}
                                    @if ($studentCoverage->has($topic->id))
                                        <span class="ml-1 text-xs font-medium text-muted-foreground">· {{ $studentCoverage->get($topic->id)->status->label() }}</span>
                                    @endif
                                </p>
                                @if ($topic->objectives)
                                    <p class="mt-1 whitespace-pre-line text-sm"><span class="font-medium">Objectives:</span> {{ $topic->objectives }}</p>
                                @endif
                                @if ($topic->content)
                                    <p class="mt-1 whitespace-pre-line text-sm text-muted-foreground">{{ $topic->content }}</p>
                                @endif
                                @if ($topic->resources)
                                    <p class="mt-1 whitespace-pre-line text-sm text-muted-foreground"><span class="font-medium">Resources:</span> {{ $topic->resources }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @empty
                <p class="text-sm text-muted-foreground">No weekly topics are planned in this syllabus yet.</p>
            @endforelse
        </slot:content>
    </april:card>

    @if ($showTracker)
        @livewire('syllabus-coverage-tracker', ['syllabus' => $syllabus], key('coverage-'.$syllabus->id))
    @endif
</div>
