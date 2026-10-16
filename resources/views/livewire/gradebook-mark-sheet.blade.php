<div class="flex flex-col gap-8">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:opacity-60';
        $learnerName = fn ($student) => $student->user?->name ?? $student->admission_number;
    @endphp

    <section class="flex flex-col gap-4" aria-labelledby="marks-heading">
        <h2 id="marks-heading" class="text-base font-semibold">Marks</h2>

        @if ($items->isEmpty())
            <p class="text-sm text-muted-foreground">No assessments yet</p>
        @elseif ($students->isEmpty())
            <p class="text-sm text-muted-foreground">No learners take this {{ strtolower(school_term('course', 'course')) }}</p>
        @else
            <div>
                <label for="mark-sheet-assessment" class="sr-only">Assessment</label>
                <select id="mark-sheet-assessment" wire:model.live="gradeItemId" class="{{ $controlClasses }}" {{ field_error_bindings('gradeItemId') }}>
                    @foreach ($items as $gradeItem)
                        <option value="{{ $gradeItem->id }}">{{ $gradeItem->name }} · {{ $gradeItem->entries_count }} of {{ $students->count() }} marked</option>
                    @endforeach
                </select>
                <x-field-error name="gradeItemId" class="mt-1" />
            </div>

            @if ($item !== null)
                <dl class="grid grid-cols-2 gap-4 border-y py-3 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-muted-foreground">Marked out of</dt>
                        <dd class="font-medium">{{ $item->gradingScale?->name ?? ($item->max_points === null ? '—' : rtrim(rtrim(number_format($item->max_points, 2), '0'), '.')) }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Category</dt>
                        <dd class="font-medium">{{ $item->category?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">Due</dt>
                        <dd class="font-medium">{{ $item->due_on?->format('j M Y') ?? '—' }}</dd>
                    </div>
                </dl>

                <x-field-error name="sheet" />

                <form wire:submit="save" class="flex flex-col gap-4" aria-label="Marks for {{ $item->name }}">
                    <ul class="divide-y border-y">
                        @foreach ($students as $student)
                            @php
                                $mark = $marks[$student->id] ?? null;
                                $state = \App\Enums\GradeEntryState::tryFrom($mark['state'] ?? '');
                                $isChanged = in_array($student->id, $changed, true);
                            @endphp
                            <li wire:key="mark-{{ $item->id }}-{{ $student->id }}" @class(['flex flex-col gap-2 py-3 sm:flex-row sm:items-start sm:gap-4', 'bg-muted/40' => $isChanged])>
                                <div class="min-w-0 flex-1 sm:pt-2.5">
                                    <p class="truncate text-sm font-medium">{{ $learnerName($student) }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $student->admission_number ?? '—' }}</p>
                                </div>

                                @if ($canManage && $mark !== null)
                                    <div class="flex w-full flex-col gap-1 sm:w-auto">
                                        <div class="grid grid-cols-[minmax(0,1fr)_9rem] gap-2 sm:w-[22rem]">
                                            @if ($item->type === \App\Enums\GradeItemType::Numeric)
                                                <input type="number" wire:model="marks.{{ $student->id }}.points" min="0" step="0.01" @if ($item->max_points !== null) max="{{ $item->max_points }}" @endif inputmode="decimal" placeholder="Mark" @disabled($state !== null && !$state->needsPoints())
                                                    aria-label="{{ $item->name }} mark for {{ $learnerName($student) }}" class="{{ $controlClasses }} text-right tabular-nums" {{ field_error_bindings('marks.'.$student->id) }}>
                                            @elseif ($item->type === \App\Enums\GradeItemType::Scale)
                                                <select wire:model="marks.{{ $student->id }}.option" @disabled($state !== null && !$state->needsPoints())
                                                    aria-label="{{ $item->name }} grade for {{ $learnerName($student) }}" class="{{ $controlClasses }}" {{ field_error_bindings('marks.'.$student->id) }}>
                                                    <option value="">Grade</option>
                                                    @foreach ($item->gradingScale?->options ?? [] as $option)
                                                        <option value="{{ $option->id }}">{{ $option->label }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input wire:model="marks.{{ $student->id }}.comment" maxlength="5000" placeholder="Comment"
                                                    aria-label="{{ $item->name }} comment for {{ $learnerName($student) }}" class="{{ $controlClasses }}" {{ field_error_bindings('marks.'.$student->id) }}>
                                            @endif
                                            <select wire:model.live="marks.{{ $student->id }}.state" aria-label="State for {{ $learnerName($student) }}" class="{{ $controlClasses }}">
                                                @foreach ($states as $entryState)
                                                    <option value="{{ $entryState->value }}">{{ $entryState->label() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <x-field-error name="marks.{{ $student->id }}" />
                                    </div>
                                @else
                                    <p class="text-sm sm:pt-2.5 sm:text-right">
                                        @if ($state !== null && !$state->needsPoints())
                                            <span class="text-muted-foreground">{{ $state->label() }}</span>
                                        @elseif ($item->type === \App\Enums\GradeItemType::Scale)
                                            {{ $item->gradingScale?->options->firstWhere('id', (int) ($mark['option'] ?? 0))?->label ?? '—' }}
                                        @elseif ($item->type === \App\Enums\GradeItemType::Text)
                                            {{ ($mark['comment'] ?? '') === '' ? '—' : $mark['comment'] }}
                                        @else
                                            <span class="tabular-nums">{{ ($mark['points'] ?? '') === '' ? '—' : $mark['points'] }}</span>
                                        @endif
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @if ($canManage)
                        <div class="sticky bottom-0 z-10 flex flex-wrap items-center justify-end gap-3 border-t bg-background py-3">
                            @if ($changed !== [])
                                <p class="mr-auto text-sm text-muted-foreground" aria-live="polite">{{ count($changed) }} not saved</p>
                                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="discard">Put back</april:button>
                            @endif
                            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save marks</april:button>
                        </div>
                    @endif
                </form>
            @endif
        @endif
    </section>

    @if ($students->isNotEmpty() && $items->isNotEmpty())
        <section class="flex flex-col gap-4" aria-labelledby="results-heading">
            <h2 id="results-heading" class="text-base font-semibold">Results</h2>
            <ul class="divide-y border-y">
                @foreach ($students as $student)
                    @php
                        $official = $officialResults->get($student->id);
                        $latest = $latestResults->get($student->id);
                        $isWaiting = $latest?->approval_status === \App\Enums\ResultApprovalStatus::Pending;
                        $isSentBack = $latest?->approval_status === \App\Enums\ResultApprovalStatus::Rejected;
                    @endphp
                    <li wire:key="result-{{ $student->id }}" class="flex flex-col gap-2 py-3">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $learnerName($student) }}</p>
                                <p class="text-xs text-muted-foreground">
                                    @if ($isWaiting)
                                        Revision {{ $latest->revision }} waiting for approval{{ $latest->percentage === null ? '' : ' · '.number_format($latest->percentage, 2).'%' }}
                                    @elseif ($isSentBack)
                                        <span class="text-destructive">Revision {{ $latest->revision }} sent back{{ $latest->approval_reason ? ': '.$latest->approval_reason : '' }}</span>
                                    @elseif ($official !== null)
                                        Revision {{ $official->revision }} · approved {{ ($official->approved_at ?? $official->published_at)?->format('j M Y') }}
                                    @else
                                        Not sent yet
                                    @endif
                                </p>
                            </div>
                            <p class="text-sm font-semibold tabular-nums">{{ $official?->percentage === null ? '—' : number_format($official->percentage, 2).'%' }}</p>

                            @if ($isWaiting && $canApprove && $latest->published_by !== auth()->id())
                                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="approveResult({{ $latest->id }})" wire:loading.attr="disabled" wire:target="approveResult">Approve</april:button>
                            @endif
                            @if ($canSubmit || ($isWaiting && $canApprove))
                                <april:dropdown-menu>
                                    <slot:trigger>
                                        <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $learnerName($student) }}">
                                            <x-lucide-ellipsis class="size-4" />
                                        </april:button>
                                    </slot:trigger>
                                    <slot:content align="end">
                                        @if ($canSubmit)
                                            <april:dropdown-menu-item wire:click="submitResult({{ $student->id }})">
                                                <x-lucide-send class="mr-2 size-4" />{{ $latest === null ? 'Send for approval' : 'Send a new revision' }}
                                            </april:dropdown-menu-item>
                                        @endif
                                        @if ($isWaiting && $canApprove)
                                            <april:dropdown-menu-item class="text-destructive" wire:click="startRejecting({{ $latest->id }})">
                                                <x-lucide-undo-2 class="mr-2 size-4" />Send back
                                            </april:dropdown-menu-item>
                                        @endif
                                    </slot:content>
                                </april:dropdown-menu>
                            @endif
                        </div>

                        @if ($isWaiting && $rejectingResultId === $latest->id)
                            <form wire:submit="rejectResult" class="flex flex-col gap-2 sm:flex-row sm:items-start" aria-label="Send back the result for {{ $learnerName($student) }}">
                                <div class="flex-1">
                                    <label for="reject-reason-{{ $latest->id }}" class="sr-only">Reason to send back the result for {{ $learnerName($student) }}</label>
                                    <input id="reject-reason-{{ $latest->id }}" wire:model="rejectReason" maxlength="500" required placeholder="What must the teacher change?" class="{{ $controlClasses }}" {{ field_error_bindings('rejectReason') }}>
                                    <x-field-error name="rejectReason" class="mt-1" />
                                </div>
                                <div class="flex gap-2">
                                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="stopRejecting">Cancel</april:button>
                                    <april:button type="submit" variant="destructive" class="h-11 select-none" wire:loading.attr="disabled" wire:target="rejectResult">Send back</april:button>
                                </div>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
