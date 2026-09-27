<div class="mx-auto flex w-full max-w-4xl flex-col gap-10">
    @php
        $locale = app()->getLocale();
        $dash = '—';
        $section = $feeInvoice->studentRecord?->academicCycleSection;
        $controlClasses = 'h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
        $moneyInput = $controlClasses.' text-right tabular-nums';
    @endphp

    <section aria-label="Invoice details" class="flex flex-col gap-6">
        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-muted-foreground">
            <span class="font-medium text-foreground">{{ $feeInvoice->user?->name ?? $dash }}</span>
            @if ($feeInvoice->studentRecord?->admission_number)
                <span>· {{ $feeInvoice->studentRecord->admission_number }}</span>
            @endif
            @if ($section !== null)
                <span>· {{ $section->academicLevel?->name }} {{ $section->label ?? $section->name }}</span>
            @endif
            <span>· Issued {{ $feeInvoice->issue_date->format('j M Y') }}</span>
        </p>

        <form wire:submit="saveDetails" class="flex flex-col gap-3" aria-label="Due date and note">
            <div class="grid gap-3 sm:grid-cols-[12rem_1fr]">
                <div>
                    <label for="due-date" class="text-sm text-muted-foreground">Due</label>
                    <input type="date" id="due-date" wire:model="dueDate" min="{{ $feeInvoice->issue_date->format('Y-m-d') }}" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('dueDate') }}>
                </div>
                <div>
                    <label for="note" class="text-sm text-muted-foreground">Note</label>
                    <input id="note" wire:model="note" maxlength="10000" placeholder="Note (optional)" class="{{ $controlClasses }} mt-1" {{ field_error_bindings('note') }}>
                </div>
            </div>
            <x-field-error name="dueDate" />
            <x-field-error name="note" />
            <div class="flex sm:justify-end">
                <april:button type="submit" :variant="$isAdding || $editingLineId !== null ? 'outline' : 'default'" class="h-11 w-full select-none sm:w-auto" wire:loading.attr="disabled" wire:target="saveDetails">Save</april:button>
            </div>
        </form>
    </section>

    <section aria-labelledby="fees-heading" class="flex flex-col gap-3">
        <div class="flex items-center justify-between gap-4">
            <h2 id="fees-heading" class="text-base font-semibold">
                Fees
                @if ($isPosted)
                    <span class="inline-flex items-center gap-1 font-normal text-muted-foreground"><x-lucide-lock class="size-3.5" aria-hidden="true" /> Posted</span>
                @endif
            </h2>
            @if (!$isPosted && $canAddLines && !$isAdding)
                <april:button type="button" variant="outline" class="h-11 select-none" wire:click="$set('isAdding', true)">Add fee</april:button>
            @endif
        </div>

        <x-field-error name="lines" />

        @if ($feeInvoice->feeInvoiceRecords->isEmpty())
            <p class="text-sm text-muted-foreground">No fees</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($feeInvoice->feeInvoiceRecords as $record)
                    @php
                        $canEditLine = !$isPosted && auth()->user()->can('update', $record);
                        $canRemoveLine = !$isPosted && !$record->paid->isPositive() && auth()->user()->can('delete', $record);
                    @endphp
                    <li wire:key="line-{{ $record->id }}" class="flex flex-col gap-3 py-2">
                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0 text-sm">
                                <p class="truncate font-medium">{{ $record->fee?->name ?? $dash }}</p>
                                <p class="tabular-nums text-muted-foreground">
                                    {{ $record->amount->formatToLocale($locale) }}
                                    @if ($record->waiver->isPositive()) · {{ $record->waiver->formatToLocale($locale) }} waived @endif
                                    @if ($record->fine->isPositive()) · {{ $record->fine->formatToLocale($locale) }} fine @endif
                                    @if ($record->paid->isPositive()) · {{ $record->paid->formatToLocale($locale) }} paid @endif
                                </p>
                            </div>
                            <div class="flex shrink-0 items-center gap-2">
                                <span class="text-sm font-medium tabular-nums">{{ $record->payable->formatToLocale($locale) }}</span>
                                @if ($canEditLine || $canRemoveLine)
                                    <april:dropdown-menu>
                                        <slot:trigger>
                                            <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="Actions for {{ $record->fee?->name }}">
                                                <x-lucide-ellipsis class="size-4" />
                                            </april:button>
                                        </slot:trigger>
                                        <slot:content align="end">
                                            @if ($canEditLine)
                                                <april:dropdown-menu-item wire:click="startEditingLine({{ $record->id }})"><x-lucide-pencil class="mr-2 size-4" />Change</april:dropdown-menu-item>
                                            @endif
                                            @if ($canRemoveLine)
                                                <april:dropdown-menu-item class="text-destructive" wire:click="removeLine({{ $record->id }})" wire:confirm="Remove {{ $record->fee?->name }}? The invoice will ask for {{ $record->payable->formatToLocale($locale) }} less.">
                                                    <x-lucide-trash-2 class="mr-2 size-4" />Remove
                                                </april:dropdown-menu-item>
                                            @endif
                                        </slot:content>
                                    </april:dropdown-menu>
                                @endif
                            </div>
                        </div>

                        @if ($canEditLine && $editingLineId === $record->id)
                            <form wire:submit="saveLine" class="flex flex-col gap-3" aria-label="Change {{ $record->fee?->name }}">
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label for="line-amount" class="text-xs text-muted-foreground">Amount</label>
                                        <input type="number" id="line-amount" min="1" step="1" wire:model="lineAmount" class="{{ $moneyInput }} mt-1" {{ field_error_bindings('lineAmount') }}>
                                    </div>
                                    <div>
                                        <label for="line-waiver" class="text-xs text-muted-foreground">Waiver</label>
                                        <input type="number" id="line-waiver" min="0" step="1" wire:model="lineWaiver" class="{{ $moneyInput }} mt-1" {{ field_error_bindings('lineWaiver') }}>
                                    </div>
                                    <div>
                                        <label for="line-fine" class="text-xs text-muted-foreground">Fine</label>
                                        <input type="number" id="line-fine" min="0" step="1" wire:model="lineFine" class="{{ $moneyInput }} mt-1" {{ field_error_bindings('lineFine') }}>
                                    </div>
                                </div>
                                <x-field-error name="lineAmount" />
                                <x-field-error name="lineWaiver" />
                                <x-field-error name="lineFine" />
                                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancel">Cancel</april:button>
                                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="saveLine">Save fee</april:button>
                                </div>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if (!$isPosted && $canAddLines && $isAdding)
            <form wire:submit="addLine" class="flex flex-col gap-3 pt-2" aria-label="Add a fee">
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label for="fee-category" class="sr-only">Fee category</label>
                        <select id="fee-category" wire:model.live="feeCategoryId" class="{{ $controlClasses }}">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="fee" class="sr-only">Fee</label>
                        <select id="fee" wire:model="feeId" class="{{ $controlClasses }}" {{ field_error_bindings('feeId') }}>
                            <option value="">{{ $fees->isEmpty() ? 'No fees left in this category' : 'Choose a fee' }}</option>
                            @foreach ($fees as $fee)
                                <option value="{{ $fee->id }}">{{ $fee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label for="new-amount" class="sr-only">Amount</label>
                        <input type="number" id="new-amount" min="1" step="1" wire:model="newAmount" placeholder="Amount" class="{{ $moneyInput }}" {{ field_error_bindings('newAmount') }}>
                    </div>
                    <div>
                        <label for="new-waiver" class="sr-only">Waiver</label>
                        <input type="number" id="new-waiver" min="0" step="1" wire:model="newWaiver" placeholder="Waiver" class="{{ $moneyInput }}" {{ field_error_bindings('newWaiver') }}>
                    </div>
                    <div>
                        <label for="new-fine" class="sr-only">Fine</label>
                        <input type="number" id="new-fine" min="0" step="1" wire:model="newFine" placeholder="Fine" class="{{ $moneyInput }}" {{ field_error_bindings('newFine') }}>
                    </div>
                </div>
                <x-field-error name="feeId" />
                <x-field-error name="newAmount" />
                <x-field-error name="newWaiver" />
                <x-field-error name="newFine" />
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <april:button type="button" variant="ghost" class="h-11 select-none" wire:click="cancel">Cancel</april:button>
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="addLine">Add fee</april:button>
                </div>
            </form>
        @endif
    </section>
</div>
