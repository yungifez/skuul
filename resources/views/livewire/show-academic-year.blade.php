<div class="space-y-10">
    @php
        $dateRange = static fn ($startsOn, $endsOn): string => $startsOn === null && $endsOn === null
            ? 'No dates'
            : ($startsOn?->format('M j, Y') ?? '—').' – '.($endsOn?->format('M j, Y') ?? '—');
        $currentStepIndex = collect($lifecycleSteps)->search(fn (array $step): bool => $step['status'] === $academicYear->status);
    @endphp

    <section class="space-y-6" aria-labelledby="academic-year-heading">
        <h2 id="academic-year-heading" class="sr-only">{{ $academicYear->name }}</h2>

        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm" aria-label="{{ school_term('academic_year', 'School year') }} status">
            @foreach ($lifecycleSteps as $step)
                <li @class([
                    'flex items-center gap-2',
                    'font-semibold text-foreground' => $loop->index === $currentStepIndex,
                    'text-foreground/70' => $currentStepIndex !== false && $loop->index < $currentStepIndex,
                    'text-muted-foreground/60' => $currentStepIndex === false || $loop->index > $currentStepIndex,
                ]) @if ($loop->index === $currentStepIndex) aria-current="step" @endif>
                    @if ($currentStepIndex !== false && $loop->index < $currentStepIndex)
                        <x-lucide-check class="size-3.5" />
                    @elseif ($loop->index === $currentStepIndex)
                        <span class="size-2 rounded-full bg-foreground"></span>
                    @endif
                    {{ $step['title'] }}
                    @unless ($loop->last)
                        <x-lucide-chevron-right class="size-3.5 text-muted-foreground/50" aria-hidden="true" />
                    @endunless
                </li>
            @endforeach
        </ol>

        <div class="flex flex-col gap-4 border-y py-4 lg:flex-row lg:items-center lg:justify-between">
            <dl class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm sm:flex sm:flex-wrap">
                <div>
                    <dt class="text-muted-foreground">Dates</dt>
                    <dd @class(['font-medium tabular-nums', 'text-muted-foreground' => $academicYear->starts_on === null && $academicYear->ends_on === null])>{{ $dateRange($academicYear->starts_on, $academicYear->ends_on) }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">{{ school_terms('period', 'Terms') }}</dt>
                    <dd class="font-medium tabular-nums">{{ $topLevelPeriods->count() }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Teaching setup</dt>
                    <dd><a href="{{ route('academic-years.instructional-model.edit', $academicYear) }}" class="font-medium underline-offset-4 hover:underline">Edit</a></dd>
                </div>
            </dl>

            <div class="flex flex-wrap items-center gap-2">
                @if ($canRollForwardSetup)
                    <april:button type="button" variant="ghost" wire:click="openSetupRolloverDialog" wire:loading.attr="disabled">
                        <x-lucide-copy-plus class="mr-2 size-4" />
                        Copy setup from {{ $previousAcademicYear->name }}
                    </april:button>
                @endif
                @if ($isDraft && $canEditCalendar)
                    <april:button-link href="{{ route('academic-years.edit', $academicYear) }}" variant="outline">Edit draft</april:button-link>
                    <button type="button" wire:click="publishCalendar" wire:loading.attr="disabled" @class([
                        'inline-flex h-10 select-none items-center justify-center rounded-md px-4 text-sm font-medium disabled:opacity-50',
                        'border border-input bg-background hover:bg-accent' => $canContinueSetup,
                        'bg-primary text-primary-foreground hover:bg-primary/90' => !$canContinueSetup,
                    ])>
                        <span wire:loading.remove wire:target="publishCalendar">Publish calendar</span>
                        <span wire:loading wire:target="publishCalendar">Publishing…</span>
                    </button>
                @endif
                <x-academic-period-status-control :period="$academicYear" route-prefix="academic-years" :show-status="false" />
            </div>
        </div>
        <x-field-error name="calendar" />
        <x-field-error name="rollover" />

        @if ($canContinueSetup)
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm"><span class="text-muted-foreground">Continue setup · Next:</span> <span class="font-medium">{{ $nextSetupStep->label() }}</span></p>
                <april:button-link href="{{ route('academic-years.setup', [$academicYear, $nextSetupStep->value]) }}">
                    Continue to {{ strtolower($nextSetupStep->label()) }}
                    <x-lucide-arrow-right class="ml-1.5 size-4" />
                </april:button-link>
            </div>
        @endif
    </section>

    <section class="space-y-2" aria-labelledby="reporting-periods-heading">
        <div class="flex min-h-10 flex-wrap items-center justify-between gap-2">
            <h2 id="reporting-periods-heading" class="font-semibold">{{ school_terms('period', 'Reporting periods') }}</h2>
            @if ($canCreatePeriods)
                <april:button-link href="{{ route('academic-periods.create') }}" variant="outline" size="sm">
                    <x-lucide-plus class="mr-1.5 size-4" />
                    Add {{ strtolower(school_term('period', 'period')) }}
                </april:button-link>
            @endif
        </div>

        @if ($topLevelPeriods->isEmpty())
            <p class="border-y py-4 text-sm text-muted-foreground">No {{ strtolower(school_terms('period', 'periods')) }} yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($topLevelPeriods as $period)
                    @php
                        $canUpdatePeriod = auth()->user()->can('update', $period);
                        $canClosePeriod = $period->status === \App\Enums\AcademicPeriodStatus::Open && auth()->user()->can('close', $period);
                        $needsInlineControl = in_array($period->status, [\App\Enums\AcademicPeriodStatus::Closing, \App\Enums\AcademicPeriodStatus::Closed], true);
                    @endphp
                    <li wire:key="period-row-{{ $period->id }}" class="grid min-h-12 grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-1 py-2 sm:grid-cols-[minmax(0,1fr)_7rem_minmax(0,16rem)_auto_2.75rem]">
                        <span class="min-w-0 truncate font-medium">{{ $period->displayName }}</span>
                        <span class="hidden text-sm text-muted-foreground sm:block">{{ $period->typeLabel }}</span>
                        <span @class(['col-start-1 row-start-2 text-sm tabular-nums sm:col-start-auto sm:row-start-auto', 'text-muted-foreground' => $period->starts_on === null && $period->ends_on === null])>{{ $dateRange($period->starts_on, $period->ends_on) }}</span>
                        <span class="hidden sm:block">
                            @if ($needsInlineControl)
                                <x-academic-period-status-control :period="$period" route-prefix="academic-periods" />
                            @elseif ($period->status !== $academicYear->status)
                                <span class="text-sm text-muted-foreground">{{ $period->status->label() }}</span>
                            @endif
                        </span>
                        <span class="col-start-2 row-span-2 row-start-1 flex justify-end sm:col-start-auto sm:row-span-1 sm:row-start-auto">
                            @if ($canUpdatePeriod || $canClosePeriod)
                                <april:dropdown-menu>
                                    <slot:trigger>
                                        <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="Actions for {{ $period->displayName }}">
                                            <x-lucide-ellipsis class="size-4" />
                                        </april:button>
                                    </slot:trigger>
                                    <slot:content>
                                        @if ($canUpdatePeriod)
                                            <april:dropdown-menu-item x-on:click="window.location.href = '{{ route('academic-periods.edit', $period) }}'">
                                                <x-lucide-calendar-cog class="mr-2 size-4" />Edit dates
                                            </april:dropdown-menu-item>
                                        @endif
                                        @if ($canClosePeriod)
                                            <form action="{{ route('academic-periods.begin-closing', $period) }}" method="POST">
                                                @csrf
                                                <april:dropdown-menu-item type="submit">
                                                    <x-lucide-lock class="mr-2 size-4" />Start closing
                                                </april:dropdown-menu-item>
                                            </form>
                                        @endif
                                    </slot:content>
                                </april:dropdown-menu>
                            @endif
                        </span>
                        @if ($needsInlineControl)
                            <span class="col-span-2 sm:hidden">
                                <x-academic-period-status-control :period="$period" route-prefix="academic-periods" />
                            </span>
                        @elseif ($period->status !== $academicYear->status)
                            <span class="col-span-2 text-sm text-muted-foreground sm:hidden">{{ $period->status->label() }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="space-y-2" aria-labelledby="calendar-exams-heading">
        <div class="flex min-h-10 flex-wrap items-center justify-between gap-2">
            <h2 id="calendar-exams-heading" class="font-semibold">Exams</h2>
            @if ($canCreateExams)
                <april:button-link href="{{ route('exams.create', ['academic_year_id' => $academicYear->id]) }}" variant="outline" size="sm">
                    <x-lucide-plus class="mr-1.5 size-4" />
                    Add exam
                </april:button-link>
            @endif
        </div>
        <div wire:key="{{ $id }}-{{ $this->tableRevision }}">
            <april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)">
                <slot:empty>
                    <p>No exams yet.</p>
                </slot:empty>
                <slot:actions>
                    <x-table-actions :items="array_filter([
                        $canEditExams ? ['label' => 'Edit exam', 'icon' => 'settings', 'url' => 'edit_url'] : null,
                        ['label' => 'View exam', 'icon' => 'eye', 'url' => 'view_url'],
                        $canDeleteExams ? ['label' => 'Delete exam', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete :name?', 'names' => 'row.name'] : null,
                    ])" />
                </slot:actions>
            </april:data-table>
        </div>
    </section>

    <april:dialog dismissable x-effect="open = $wire.showSetupRolloverDialog">
        <slot:content class="sm:max-w-2xl">
            <april:dialog-header>
                <slot:title>Review setup from {{ $previousAcademicYear?->name }}</slot:title>
                <slot:description>Nothing is created until you confirm. Existing setup in {{ $academicYear->name }} is left unchanged.</slot:description>
            </april:dialog-header>

            @if ($setupRolloverPreview !== null)
                <div class="divide-y border-y">
                    @foreach ($setupRolloverPreview['items'] as $item)
                        <div wire:key="rollover-item-{{ $item['key'] }}" class="py-3">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-medium">{{ $item['title'] }}</h3>
                                    <p class="mt-1 text-sm text-muted-foreground">{{ $item['description'] }}</p>
                                </div>
                                @if ($item['will_create'])
                                    <april:badge variant="secondary">{{ $item['count'] }} {{ $item['count'] === 1 ? 'item' : 'items' }}</april:badge>
                                @else
                                    <april:badge variant="outline">No changes</april:badge>
                                @endif
                            </div>
                            <ul class="mt-3 space-y-1 text-sm">
                                @foreach ($item['details'] as $detail)
                                    <li class="flex gap-2 text-muted-foreground"><span aria-hidden="true">•</span><span>{{ $detail }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-sm">
                    <p class="font-medium">Never copied</p>
                    <p class="mt-1 text-muted-foreground">Learners, placements, teacher assignments, {{ strtolower(school_terms('class_level', 'classes')) }}, subjects, exams, timetables, attendance and results. Set those up for this year when you are ready.</p>
                </div>
            @endif

            <april:dialog-footer>
                <april:button type="button" variant="outline" wire:click="$set('showSetupRolloverDialog', false)">
                    Cancel
                </april:button>
                @if (($setupRolloverPreview['create_count'] ?? 0) > 0)
                    <april:button type="button" wire:click="rollForwardSetup" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="rollForwardSetup">Create listed setup</span>
                        <span wire:loading wire:target="rollForwardSetup">Creating setup…</span>
                    </april:button>
                @endif
            </april:dialog-footer>
        </slot:content>
    </april:dialog>
</div>
