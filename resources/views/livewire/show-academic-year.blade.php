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
                <div class="rounded-lg border p-4"><p class="text-muted-foreground">Reporting periods</p><p class="mt-1 font-semibold">{{ $topLevelPeriods->count() }}</p></div>
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
                    </article>
                @empty
                    <p class="text-sm text-muted-foreground">No reporting periods have been configured.</p>
                @endforelse
            </div>
        </slot:content>
    </april:card>

    <april:card>
        <slot:title>Exams in this calendar</slot:title>
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
</div>
