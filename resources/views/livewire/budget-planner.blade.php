<div class="flex flex-col gap-10">
    @php
        $controlClasses = 'mt-1 h-11 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring';
    @endphp

    <section class="flex flex-col gap-3" aria-labelledby="plans-heading">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 id="plans-heading" class="text-base font-semibold">{{ $academicYear?->name ?? 'No cycle yet' }}</h2>
                <p class="text-sm text-muted-foreground">Each plan beside what the books say happened. Plans running over come first.</p>
            </div>
            @if ($academicYears->count() > 1)
                <div class="w-full sm:w-56">
                    <label for="academic_year_id" class="text-sm text-muted-foreground">Cycle</label>
                    <select id="academic_year_id" wire:model.live="academicYearId" class="{{ $controlClasses }}">
                        @foreach ($academicYears as $cycle)
                            <option value="{{ $cycle->id }}" @selected($cycle->id === $academicYear?->id)>{{ $cycle->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        @if ($rows->isEmpty())
            <p class="text-sm text-muted-foreground">No budgets for this cycle yet.</p>
        @else
            <ul class="divide-y border-y">
                @foreach ($rows as $row)
                    <li wire:key="budget-{{ $row->budget->id }}" class="flex items-center gap-3 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $row->budget->account?->name ?? '—' }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ $row->budget->coverage() }} · {{ $row->budget->narrowedTo() }}</p>
                        </div>
                        <div class="shrink-0 text-right text-sm tabular-nums">
                            <p class="{{ $row->isOverspent() ? 'font-semibold text-destructive' : '' }}">
                                {{ money_text($row->actual) }} <span class="text-muted-foreground">of {{ money_text($row->planned) }}</span>
                            </p>
                            <p class="text-xs {{ $row->isOverspent() ? 'text-destructive' : 'text-muted-foreground' }}">
                                {{ $row->used() === null ? '—' : $row->used().'% used' }}
                                @if ($row->isOverspent())
                                    · over by {{ money_text(abs($row->difference())) }}
                                @endif
                            </p>
                        </div>
                        @if ($canWrite)
                            <april:dropdown-menu>
                                <slot:trigger>
                                    <april:button type="button" variant="ghost" size="icon" class="size-11 select-none" aria-label="More for {{ $row->budget->account?->name ?? 'this plan' }}">
                                        <x-lucide-ellipsis class="size-4" />
                                    </april:button>
                                </slot:trigger>
                                <slot:content align="end">
                                    <april:dropdown-menu-item wire:click="revise({{ $row->budget->id }})"><x-lucide-pencil class="mr-2 size-4" />Revise</april:dropdown-menu-item>
                                    <april:dropdown-menu-item wire:click="remove({{ $row->budget->id }})" wire:confirm="Remove the budget for {{ $row->budget->account?->name ?? 'this account' }}? What was spent stays in the books."><x-lucide-trash-2 class="mr-2 size-4" />Remove</april:dropdown-menu-item>
                                </slot:content>
                            </april:dropdown-menu>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($canWrite && $academicYear !== null)
        <section class="flex flex-col gap-3" aria-labelledby="write-heading">
            <h2 id="write-heading" class="text-base font-semibold">Write or revise a plan</h2>
            <p class="text-sm text-muted-foreground">The same account, stretch and narrowing revises the plan already there.</p>
            <form wire:submit="save" class="flex flex-col gap-4" aria-label="Write a budget">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="ledger_account_id" class="text-sm text-muted-foreground">Account</label>
                        <select id="ledger_account_id" wire:model="ledgerAccountId" required class="{{ $controlClasses }}" {{ field_error_bindings('ledgerAccountId') }}>
                            <option value="">Choose an account</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="ledgerAccountId" class="mt-1" />
                    </div>
                    <div>
                        <label for="amount" class="text-sm text-muted-foreground">Amount</label>
                        <input id="amount" type="number" inputmode="decimal" step="0.01" min="0" wire:model="amount" required class="{{ $controlClasses }}" {{ field_error_bindings('amount') }}>
                        <x-field-error name="amount" class="mt-1" />
                    </div>
                    <div>
                        <label for="academic_period_id" class="text-sm text-muted-foreground">Covers</label>
                        <select id="academic_period_id" wire:model="academicPeriodId" class="{{ $controlClasses }}" {{ field_error_bindings('academicPeriodId') }}>
                            <option value="">The whole year</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->id }}">{{ $period->name }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="academicPeriodId" class="mt-1" />
                    </div>
                    <div>
                        <label for="program_id" class="text-sm text-muted-foreground">Programme (optional)</label>
                        <select id="program_id" wire:model="programId" class="{{ $controlClasses }}" {{ field_error_bindings('programId') }}>
                            <option value="">Everything on this account</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->name }}</option>
                            @endforeach
                        </select>
                        <x-field-error name="programId" class="mt-1" />
                    </div>
                    <div>
                        <label for="fund" class="text-sm text-muted-foreground">Fund (optional)</label>
                        <input id="fund" wire:model="fund" maxlength="60" placeholder="Library fund" class="{{ $controlClasses }}" {{ field_error_bindings('fund') }}>
                        <x-field-error name="fund" class="mt-1" />
                    </div>
                    <div>
                        <label for="note" class="text-sm text-muted-foreground">Note (optional)</label>
                        <input id="note" wire:model="note" maxlength="1000" class="{{ $controlClasses }}" {{ field_error_bindings('note') }}>
                        <x-field-error name="note" class="mt-1" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <april:button type="submit" class="h-11 select-none" wire:loading.attr="disabled" wire:target="save">Save the plan</april:button>
                </div>
            </form>
        </section>
    @endif
</div>
