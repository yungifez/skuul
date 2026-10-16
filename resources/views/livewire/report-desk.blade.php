<div class="flex flex-col gap-10" @if ($isBuilding) wire:poll.5s @endif>
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    @if ($canRequest)
        <section class="flex flex-col gap-3" aria-labelledby="ask-heading">
            <h2 id="ask-heading" class="text-base font-semibold">Ask for a report</h2>
            <p class="text-sm text-muted-foreground">A worker builds it, so the screen never waits. Every request and download goes in the audit log.</p>
            <form wire:submit="build" class="flex flex-col gap-3" aria-label="Ask for a report">
                <div class="grid gap-3 sm:grid-cols-3">
                    <div>
                        <label for="type" class="text-sm text-muted-foreground">Report</label>
                        <select id="type" wire:model="type" required class="{{ $controlClasses }}" {{ field_error_bindings('type') }}>
                            <option value="">Choose a report</option>
                            @foreach ($reports as $key => $title)
                                <option value="{{ $key }}">{{ $title }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="type" class="mt-1" />
                    </div>
                    <div>
                        <label for="format" class="text-sm text-muted-foreground">Shape</label>
                        <select id="format" wire:model="format" class="{{ $controlClasses }}" {{ field_error_bindings('format') }}>
                            @foreach ($formats as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="format" class="mt-1" />
                    </div>
                    <div>
                        <label for="financial_period_id" class="text-sm text-muted-foreground">Financial period</label>
                        <select id="financial_period_id" wire:model="financialPeriodId" class="{{ $controlClasses }}" {{ field_error_bindings('financialPeriodId') }}>
                            <option value="">Current open period</option>
                            @foreach ($financialPeriods as $financialPeriod)
                                <option value="{{ $financialPeriod->id }}">{{ $financialPeriod->name }}{{ $financialPeriod->isClosed() ? ' · Closed' : '' }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="financialPeriodId" class="mt-1" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="build">
                        <x-lucide-play class="mr-2 size-4" />Build it
                    </april:button>
                </div>
            </form>
        </section>
    @endif

    <section class="flex flex-col gap-3" aria-labelledby="runs-heading">
        <h2 id="runs-heading" class="text-base font-semibold">What has been asked for</h2>

        @if ($runs->isEmpty())
            <p class="text-sm text-muted-foreground">Nobody has asked for a report yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($runs as $run)
                    <li wire:key="run-{{ $run->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $reports[$run->type] ?? $run->type }} <span class="uppercase text-muted-foreground">· {{ $run->format }}</span></p>
                            <p class="truncate text-xs {{ $run->status === \App\Enums\ReportStatus::Failed ? 'text-destructive' : 'text-muted-foreground' }}">
                                Number {{ $run->id }} · {{ $run->status->label() }} · {{ $run->row_count === null ? '—' : $run->row_count.' rows' }} · {{ $run->requestedBy?->name ?? '—' }} · {{ $run->created_at?->diffForHumans() ?? '—' }}
                            </p>
                            @if ($run->error !== null)
                                <p class="break-words text-xs text-destructive">{{ $run->error }}</p>
                            @endif
                        </div>
                        @if ($run->isReady())
                            <april:button-link href="{{ route('reports.download', $run->id) }}" variant="outline" class="h-11 shrink-0 select-none" aria-label="Download number {{ $run->id }}">
                                <x-lucide-download class="size-4 sm:mr-2" /><span class="hidden sm:inline">Download</span>
                            </april:button-link>
                        @endif
                    </li>
                @endforeach
            </ul>
            {{ $runs->links('components.pagination-links-view') }}
        @endif
    </section>
</div>
