<section class="flex flex-col gap-4" aria-labelledby="roll-heading">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $plainStatuses = [\App\Enums\BoardingRollEntryStatus::Present->value, \App\Enums\BoardingRollEntryStatus::NotRecorded->value];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <h2 id="roll-heading" class="text-base font-semibold">Boarders</h2>
            <span @class([
                'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold',
                'bg-muted text-muted-foreground' => $roll->isComplete(),
            ])>{{ $roll->isComplete() ? 'Completed '.$roll->completed_at?->format('H:i') : 'In progress' }}</span>
        </div>
        <p class="text-sm text-muted-foreground" aria-live="polite">
            {{ $answered }} of {{ count($answers) }} answered
            @if ($unaccounted > 0)
                · <span class="font-medium text-destructive">{{ $unaccounted }} unaccounted</span>
            @endif
        </p>
    </div>

    <x-field-error name="roll" />

    <form wire:submit="save" class="flex flex-col gap-4" aria-label="Answers for the {{ strtolower($roll->type->label()) }}">
        <ul class="divide-y border-y">
            @foreach ($entries as $entry)
                @php
                    $boarder = $entry->studentRecord->user?->name ?? $entry->studentRecord->admission_number;
                    $answer = $answers[$entry->id] ?? null;
                    $isChanged = in_array($entry->id, $changed, true);
                    $status = \App\Enums\BoardingRollEntryStatus::tryFrom($answer['status'] ?? '');
                    $needsDetail = $answer !== null && (!in_array($answer['status'], $plainStatuses, true) || $answer['location'] !== '' || $answer['note'] !== '');
                @endphp
                <li wire:key="roll-entry-{{ $entry->id }}" @class(['flex flex-col gap-2 py-3', 'bg-muted/40' => $isChanged])>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:gap-4">
                        <div class="min-w-0 flex-1 sm:pt-2.5">
                            <p class="truncate text-sm font-medium">{{ $boarder }}</p>
                            <p class="text-xs text-muted-foreground">{{ $entry->studentRecord->admission_number ?? '—' }}</p>
                        </div>

                        @if ($canManage && $answer !== null)
                            <div class="w-full sm:w-48">
                                <select wire:model.live="answers.{{ $entry->id }}.status" aria-label="Answer for {{ $boarder }}" class="{{ $controlClasses }}" {{ field_error_bindings('answers.'.$entry->id) }}>
                                    @foreach ($statuses as $choice)
                                        <option value="{{ $choice->value }}">{{ $choice->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="text-sm sm:pt-2.5 sm:text-right">
                                <p @class(['font-medium', 'text-destructive' => $status === \App\Enums\BoardingRollEntryStatus::Unaccounted, 'text-muted-foreground' => $status === \App\Enums\BoardingRollEntryStatus::NotRecorded])>{{ $status?->label() ?? '—' }}</p>
                                @if (($answer['location'] ?? '') !== '' || ($answer['note'] ?? '') !== '')
                                    <p class="text-xs text-muted-foreground">{{ collect([$answer['location'], $answer['note']])->filter()->implode(' · ') }}</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if ($canManage && $needsDetail)
                        <div class="grid gap-2 sm:grid-cols-2">
                            <input wire:model="answers.{{ $entry->id }}.location" maxlength="150" placeholder="Where (optional)" aria-label="Where {{ $boarder }} is" class="{{ $controlClasses }}">
                            <input wire:model="answers.{{ $entry->id }}.note" maxlength="1000" placeholder="Note (optional)" aria-label="Note for {{ $boarder }}" class="{{ $controlClasses }}">
                        </div>
                    @endif

                    <x-field-error name="answers.{{ $entry->id }}" />
                </li>
            @endforeach
        </ul>

        @if ($canManage)
            <div class="sticky bottom-0 z-10 flex flex-wrap items-center justify-end gap-3 border-t bg-background py-3">
                @if ($changed !== [])
                    <p class="mr-auto text-sm text-muted-foreground" aria-live="polite">{{ count($changed) }} not saved</p>
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="discard">Put back</april:button>
                    <april:button type="submit" variant="outline" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save</april:button>
                @endif
                <april:button type="button" class="h-11 select-none" wire:click="complete" wire:confirm="Complete the roll? Answers cannot change after this." wire:loading.attr="disabled" wire:target="complete,save">Complete roll</april:button>
            </div>
        @endif
    </form>
</section>
