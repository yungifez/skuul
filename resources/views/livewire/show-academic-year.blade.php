<div class="space-y-6">
    <april:card>
        <slot:title class="flex flex-wrap items-center gap-3">
            <span>{{ $academicYear->name }}</span>
            <april:badge variant="{{ $isDraft ? 'secondary' : 'default' }}">{{ $academicYear->statusLabel() }}</april:badge>
        </slot:title>
        <slot:description>Dates, reporting periods, and readiness for this school calendar.</slot:description>
        <slot:content>
            <div class="grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border p-4"><p class="text-muted-foreground">Calendar starts</p><p class="mt-1 font-semibold">{{ $academicYear->starts_on?->format('M j, Y') ?? 'Not scheduled' }}</p></div>
                <div class="rounded-lg border p-4"><p class="text-muted-foreground">Calendar ends</p><p class="mt-1 font-semibold">{{ $academicYear->ends_on?->format('M j, Y') ?? 'Not scheduled' }}</p></div>
                <div class="rounded-lg border p-4"><p class="text-muted-foreground">{{ school_terms('period', 'Reporting periods') }}</p><p class="mt-1 font-semibold">{{ $topLevelPeriods->count() }}</p></div>
                <div class="rounded-lg border p-4"><p class="text-muted-foreground">Teaching setup</p><a href="{{ route('academic-years.instructional-model.edit', $academicYear) }}" class="mt-1 inline-flex font-semibold text-primary hover:underline">Manage setup</a></div>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                @if ($isDraft && $canEditCalendar)
                    <april:button-link href="{{ route('academic-years.edit', $academicYear) }}" variant="outline">Edit draft</april:button-link>
                    <button type="button" wire:click="publishCalendar" wire:loading.attr="disabled" class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50">
                        <span wire:loading.remove wire:target="publishCalendar">Publish calendar</span>
                        <span wire:loading wire:target="publishCalendar">Publishing…</span>
                    </button>
                @endif
                <x-academic-period-status-control :period="$academicYear" route-prefix="academic-years" />
            </div>
            @error('calendar')<p class="mt-3 text-sm text-destructive">{{ $message }}</p>@enderror
        </slot:content>
    </april:card>

    @if (current_academic_year_id() === $academicYear->id && auth()->user()->can('set academic period'))
        <april:card>
            <slot:title>Working {{ strtolower(school_term('period', 'academic period')) }}</slot:title>
            <slot:description>Choose the reporting period staff are working in. This does not change the calendar or historical records.</slot:description>
            <slot:content>@livewire('set-academic-period')</slot:content>
        </april:card>
    @endif

    <section class="rounded-xl border bg-muted/30 p-5 md:p-6" aria-labelledby="academic-year-flow-heading">
        <div class="flex items-start gap-3">
            <span class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                <x-lucide-route class="size-5" />
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h2 id="academic-year-flow-heading" class="text-lg font-semibold">How a school year moves</h2>
                    <x-help-tooltip label="School year lifecycle help">Build the year while it is a draft, schedule it when the dates are agreed, open it for daily work, then close it when the year is complete. Closing protects the year’s history.</x-help-tooltip>
                </div>
                <p class="mt-1 text-sm text-muted-foreground">The status tells you what can still be changed and whether staff can record new work.</p>
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            @foreach ($lifecycleSteps as $step)
                @php($isCurrentStep = $academicYear->status === $step['status'])
                <div class="rounded-lg border p-4 {{ $isCurrentStep ? 'border-primary bg-primary/5' : 'bg-background' }}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-semibold">{{ $step['title'] }}</span>
                        @if ($isCurrentStep)
                            <april:badge variant="default">Now</april:badge>
                        @endif
                    </div>
                    <p class="mt-2 text-xs leading-5 text-muted-foreground">{{ $step['description'] }}</p>
                </div>
            @endforeach
        </div>

        @if ($canRollForwardSetup)
            <div class="mt-5 flex flex-col justify-between gap-4 rounded-lg border bg-background p-4 sm:flex-row sm:items-center">
                <div>
                    <p class="font-medium">Starting this {{ strtolower(school_term('academic_year', 'school year')) }} from {{ $previousAcademicYear->name }}?</p>
                    <p class="mt-1 text-sm text-muted-foreground">Review the teaching approach and reporting periods that can be created from the previous {{ strtolower(school_term('academic_year', 'school year')) }}.</p>
                </div>
                <april:button type="button" variant="outline" wire:click="openSetupRolloverDialog" wire:loading.attr="disabled" class="shrink-0">
                    <x-lucide-copy-plus class="mr-2 size-4" />
                    Copy setup from previous {{ strtolower(school_term('academic_year', 'school year')) }}
                </april:button>
            </div>
        @endif
        @error('rollover')<p class="mt-3 text-sm text-destructive">{{ $message }}</p>@enderror
    </section>

    <april:card>
        <slot:title>Reporting timeline</slot:title>
        <slot:description>Reporting boundaries drive gradebooks, results, timetables, and reports.</slot:description>
        <slot:content>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @forelse ($topLevelPeriods as $period)
                    <article class="rounded-lg border p-4">
                        <div class="flex items-start justify-between gap-3"><h3 class="font-semibold">{{ $period->displayName }}</h3><span class="rounded-md bg-muted px-2 py-1 text-xs font-medium">{{ $period->typeLabel }}</span></div>
                        <p class="mt-3 text-sm text-muted-foreground">{{ $period->starts_on?->format('M j, Y') ?? 'No start date' }} – {{ $period->ends_on?->format('M j, Y') ?? 'No end date' }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">{{ $period->lengthInDays() ? $period->lengthInDays().' calendar days' : 'Dates not set' }}</p>
                        <div class="mt-4"><x-academic-period-status-control :period="$period" route-prefix="academic-periods" /></div>
                    </article>
                @empty
                    <p class="text-sm text-muted-foreground">No reporting periods have been configured.</p>
                @endforelse
            </div>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title class="flex flex-wrap items-center justify-between gap-3">
            <span>Exams in this calendar</span>
            @if ($canCreateExams)
                <april:button-link href="{{ route('exams.create', ['academic_year_id' => $academicYear->id]) }}" variant="outline" size="sm">
                    <x-lucide-plus class="mr-2 size-4" />
                    Add exam
                </april:button-link>
            @endif
        </slot:title>
        <slot:description>Exams appear here once they are assigned to one of the reporting periods.</slot:description>
        <slot:content>
            <div wire:key="{{ $id }}-{{ $this->tableRevision }}">
                <april:data-table id="{{ $id }}" :data="$data" :columns="$columns" :pagination="$pagination" :per-page-options="$perPageOptions" row-key="{{ $rowKey }}" :searchable="$searchable" @query-change="$wire.updateTable($event.detail)">
                    <slot:empty>
                        <div class="space-y-1"><p class="font-medium text-foreground">No exams in this calendar</p><p>Exams will appear here once they are created.</p></div>
                    </slot:empty>
                    <slot:actions>
                        <x-table-actions :items="array_filter([
                            $canEditExams ? ['label' => 'Edit exam', 'icon' => 'settings', 'url' => 'edit_url'] : null,
                            ['label' => 'View exam', 'icon' => 'eye', 'url' => 'view_url'],
                            $canDeleteExams ? ['label' => 'Delete exam', 'icon' => 'trash-2', 'url' => 'delete_url', 'type' => 'delete', 'confirm' => 'Delete this exam?'] : null,
                        ])" />
                    </slot:actions>
                </april:data-table>
            </div>
        </slot:content>
    </april:card>

    <april:dialog dismissable x-effect="show = $wire.showSetupRolloverDialog">
        <slot:content class="sm:max-w-2xl">
            <april:dialog-header>
                <slot:title>Review setup from {{ $previousAcademicYear?->name }}</slot:title>
                <slot:description>Nothing is created until you confirm. Existing setup in {{ $academicYear->name }} is left unchanged.</slot:description>
            </april:dialog-header>

            @if ($setupRolloverPreview !== null)
                <div class="space-y-3">
                    @foreach ($setupRolloverPreview['items'] as $item)
                        <div wire:key="rollover-item-{{ $item['key'] }}" class="rounded-lg border p-4">
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

                <div class="mt-4 rounded-lg border border-amber-500/30 bg-amber-500/10 p-4 text-sm">
                    <p class="font-semibold">These records are never copied:</p>
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
