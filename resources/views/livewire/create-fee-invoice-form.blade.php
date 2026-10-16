<form wire:submit="save" class="mx-auto flex w-full max-w-4xl flex-col gap-10" aria-label="Create fee invoices">
    @php
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $moneyInput = $controlClasses.' text-right tabular-nums';
        $studentCount = $students->count();
    @endphp

    <section aria-label="Dates and note" class="flex flex-col gap-3">
        <div class="grid gap-3 sm:grid-cols-[12rem_12rem_1fr]">
            <div>
                <label for="issue-date" class="text-sm text-muted-foreground">Issued</label>
                <input type="date" id="issue-date" wire:model.live="issueDate" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('issueDate') }}>
            </div>
            <div>
                <label for="due-date" class="text-sm text-muted-foreground">Due</label>
                <input type="date" id="due-date" wire:model="dueDate" min="{{ $issueDate }}" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('dueDate') }}>
            </div>
            <div>
                <label for="invoice-note" class="text-sm text-muted-foreground">Note</label>
                <input id="invoice-note" wire:model="note" maxlength="10000" placeholder="Note (optional)" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('note') }}>
            </div>
        </div>
        <x-field-error name="issueDate" />
        <x-field-error name="dueDate" />
        <x-field-error name="note" />
    </section>

    <section aria-labelledby="invoice-students-heading" class="flex flex-col gap-3">
        <div class="flex items-center justify-between gap-4">
            <h2 id="invoice-students-heading" class="text-base font-semibold">Students <span class="font-normal tabular-nums text-muted-foreground">{{ $studentCount }}</span></h2>
            @if ($studentCount > 1)
                <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="clearStudents">Clear</april:button>
            @endif
        </div>
        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_1fr_auto]">
            <div>
                <label for="invoice-level" class="sr-only">{{ school_term('class_level', 'Class') }}</label>
                <select id="invoice-level" wire:model.live="academicLevelId" class="{{ $controlClasses }}">
                    @forelse ($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @empty
                        <option value="">No {{ strtolower(school_terms('class_level', 'classes')) }} this year</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label for="invoice-section" class="sr-only">{{ school_term('section', 'Section') }}</label>
                <select id="invoice-section" wire:model.live="cycleSectionId" class="{{ $controlClasses }}">
                    <option value="">All {{ strtolower(school_terms('section', 'sections')) }}</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->label ?? $section->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="invoice-student" class="sr-only">Student</label>
                <select id="invoice-student" wire:model="studentRecordId" class="{{ $controlClasses }}" @disabled($cycleSectionId === '')>
                    <option value="">All students</option>
                    @foreach ($studentsToPick as $record)
                        <option value="{{ $record->id }}">{{ $record->user?->name ?? $record->admission_number }}</option>
                    @endforeach
                </select>
            </div>
            <april:button type="button" variant="outline" class="h-11 select-none" wire:click="addStudents" wire:loading.attr="disabled" wire:target="addStudents" :disabled="$levels->isEmpty()">Add</april:button>
        </div>
        <x-field-error name="studentRecordIds" />
        <x-field-error name="studentRecordIds.*" />

        @if ($studentCount === 0)
            <p class="text-sm text-muted-foreground">No students</p>
        @else
            <ul class="max-h-80 divide-y overflow-y-auto border-y">
                @foreach ($students as $record)
                    @php $section = $record->academicCycleSection; @endphp
                    <li wire:key="invoice-student-{{ $record->id }}" class="flex min-h-11 items-center justify-between gap-3 text-sm">
                        <span class="min-w-0 truncate">
                            <span class="font-medium">{{ $record->user?->name ?? '—' }}</span>
                            <span class="text-muted-foreground">· {{ $record->admission_number ?? '—' }}@if ($section) · {{ $section->academicLevel?->name }} {{ $section->label ?? $section->name }}@endif</span>
                        </span>
                        <april:button type="button" variant="ghost" size="icon" class="size-11 shrink-0 select-none" wire:click="removeStudent({{ $record->id }})" aria-label="Remove {{ $record->user?->name }}">
                            <x-lucide-x class="size-4" />
                        </april:button>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section aria-labelledby="invoice-fees-heading" class="flex flex-col gap-3">
        <h2 id="invoice-fees-heading" class="text-base font-semibold">Fees</h2>
        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
            <div>
                <label for="invoice-fee-category" class="sr-only">Fee category</label>
                <select id="invoice-fee-category" wire:model.live="feeCategoryId" class="{{ $controlClasses }}">
                    @forelse ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @empty
                        <option value="">No fee categories</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label for="invoice-fee" class="sr-only">Fee</label>
                <select id="invoice-fee" wire:model="feeId" class="{{ $controlClasses }}">
                    <option value="">All fees in this category</option>
                    @foreach ($feesToPick as $fee)
                        <option value="{{ $fee->id }}">{{ $fee->name }}</option>
                    @endforeach
                </select>
            </div>
            <april:button type="button" variant="outline" class="h-11 select-none" wire:click="addFees" wire:loading.attr="disabled" wire:target="addFees" :disabled="$categories->isEmpty()">Add</april:button>
        </div>
        <x-field-error name="lines" />

        @if ($lines === [])
            <p class="text-sm text-muted-foreground">No fees</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($lines as $feeId => $line)
                    @php $feeName = $addedFees->get($feeId)?->name ?? '—'; @endphp
                    <li wire:key="invoice-fee-{{ $feeId }}" class="flex flex-col gap-2 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <span class="min-w-0 truncate text-sm font-medium">{{ $feeName }}</span>
                            <april:button type="button" variant="ghost" size="icon" class="size-11 shrink-0 select-none" wire:click="removeFee({{ $feeId }})" aria-label="Remove {{ $feeName }}">
                                <x-lucide-x class="size-4" />
                            </april:button>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label for="fee-{{ $feeId }}-amount" class="text-xs text-muted-foreground">Amount</label>
                                <input type="number" id="fee-{{ $feeId }}-amount" aria-label="Amount for {{ $feeName }}" min="1" step="1" wire:model.blur="lines.{{ $feeId }}.amount" class="{{ $moneyInput }} mt-1" {{ field_error_bindings('lines.'.$feeId.'.amount') }}>
                            </div>
                            <div>
                                <label for="fee-{{ $feeId }}-waiver" class="text-xs text-muted-foreground">Waiver</label>
                                <input type="number" id="fee-{{ $feeId }}-waiver" aria-label="Waiver for {{ $feeName }}" min="0" step="1" wire:model.blur="lines.{{ $feeId }}.waiver" class="{{ $moneyInput }} mt-1" {{ field_error_bindings('lines.'.$feeId.'.waiver') }}>
                            </div>
                            <div>
                                <label for="fee-{{ $feeId }}-fine" class="text-xs text-muted-foreground">Fine</label>
                                <input type="number" id="fee-{{ $feeId }}-fine" aria-label="Fine for {{ $feeName }}" min="0" step="1" wire:model.blur="lines.{{ $feeId }}.fine" class="{{ $moneyInput }} mt-1" {{ field_error_bindings('lines.'.$feeId.'.fine') }}>
                            </div>
                        </div>
                        <x-field-error name="lines.{{ $feeId }}.amount" />
                        <x-field-error name="lines.{{ $feeId }}.waiver" />
                        <x-field-error name="lines.{{ $feeId }}.fine" />
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <div class="flex flex-col gap-3 border-t pt-6 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-muted-foreground tabular-nums">
            {{ $studentCount }} {{ $studentCount === 1 ? 'invoice' : 'invoices' }} · {{ money_text($perInvoice) }} each
        </p>
        <div class="flex flex-col-reverse gap-3 sm:flex-row">
            <april:button-link href="{{ route('fee-invoices.index') }}" variant="ghost" class="h-11 select-none">Cancel</april:button-link>
            <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">
                {{ $studentCount > 1 ? 'Create '.$studentCount.' invoices' : 'Create invoice' }}
            </april:button>
        </div>
    </div>
</form>
