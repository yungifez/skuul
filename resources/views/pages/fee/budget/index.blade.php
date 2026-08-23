@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('fees.index'), 'text' => 'Fees'],
    ['href' => route('budgets.index'), 'text' => 'Budgets', 'active'],
]])

@section('title', __('Budgets'))

@section('page_heading', __('Budgets'))

@section('content')
<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
            {{ $academicYear?->name ?? 'No cycle yet' }}
        </h2>
        <p class="mt-1 text-sm text-muted-foreground">
            What each account is allowed, beside what the books say happened. The plan can be revised; what happened cannot.
        </p>
    </div>

    @if ($academicYears->count() > 1)
        <form method="GET" action="{{ route('budgets.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex flex-col gap-2">
                <label for="cycle" class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Cycle</label>
                <select id="cycle" name="academic_year_id" onchange="this.form.submit()"
                    class="flex h-10 w-56 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    @foreach ($academicYears as $cycle)
                        <option value="{{ $cycle->id }}" @if ($cycle->id === $academicYear?->id) selected @endif>{{ $cycle->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    @endif

    <x-display-validation-errors />

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Budget against actual</h3>
            <p class="text-sm text-muted-foreground">A plan that is running ahead of itself is shown first.</p>
        </div>

        @if ($rows->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-wallet class="size-6" />
                </span>
                <p class="text-sm font-medium">No budgets have been written for this cycle.</p>
                <p class="max-w-sm text-sm text-muted-foreground">Write one below. You only need the account and the amount.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 font-medium">Account</th>
                            <th class="p-4 font-medium">Covers</th>
                            <th class="p-4 font-medium">Narrowed to</th>
                            <th class="p-4 font-medium">Planned</th>
                            <th class="p-4 font-medium">Actual</th>
                            <th class="p-4 font-medium">Difference</th>
                            <th class="p-4 font-medium">Used</th>
                            <th class="p-4 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr class="border-b last:border-0">
                                <td class="p-4 font-medium">{{ $row->budget->account?->name }}</td>
                                <td class="p-4 text-muted-foreground">{{ $row->budget->coverage() }}</td>
                                <td class="p-4 text-muted-foreground">{{ $row->budget->narrowedTo() }}</td>
                                <td class="p-4">{{ number_format($row->planned, 2) }}</td>
                                <td class="p-4">{{ number_format($row->actual, 2) }}</td>
                                <td class="p-4 {{ $row->isOverspent() ? 'font-semibold text-destructive' : 'text-muted-foreground' }}">
                                    {{ number_format($row->difference(), 2) }}
                                </td>
                                <td class="p-4">
                                    @if ($row->used() === null)
                                        <span class="text-muted-foreground">&mdash;</span>
                                    @else
                                        <span class="{{ $row->isOverspent() ? 'font-semibold text-destructive' : '' }}">{{ $row->used() }}%</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    @can('delete', $row->budget)
                                        <form action="{{ route('budgets.destroy', $row->budget->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <april:button type="submit" variant="ghost" size="sm">Remove</april:button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @can('create', App\Models\Budget::class)
        @if ($academicYear !== null)
            <form action="{{ route('budgets.store') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">

                <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
                    <div class="flex flex-col gap-1.5 border-b p-6">
                        <h3 class="text-lg font-semibold leading-none tracking-tight">Write a budget</h3>
                        <p class="text-sm text-muted-foreground">
                            Writing the same account and stretch of the year again revises the plan instead of adding a second one.
                        </p>
                    </div>

                    <div class="grid gap-4 p-6 sm:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label for="budget-account" class="text-sm font-medium leading-none">Account</label>
                            <select id="budget-account" name="ledger_account_id" required
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} &middot; {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="budget-amount" class="text-sm font-medium leading-none">Amount</label>
                            <input id="budget-amount" name="amount" type="number" step="0.01" min="0" required value="{{ old('amount') }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="budget-period" class="text-sm font-medium leading-none">Covers</label>
                            <select id="budget-period" name="academic_period_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                <option value="">The whole year</option>
                                @foreach ($periods as $period)
                                    <option value="{{ $period->id }}">{{ $period->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="budget-program" class="text-sm font-medium leading-none">Programme <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <select id="budget-program" name="program_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                <option value="">Everything on this account</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="budget-fund" class="text-sm font-medium leading-none">Fund <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <input id="budget-fund" name="fund" maxlength="60" value="{{ old('fund') }}"
                                placeholder="Building fund, library fund"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="budget-note" class="text-sm font-medium leading-none">Note <span class="font-normal text-muted-foreground">(optional)</span></label>
                            <input id="budget-note" name="note" maxlength="1000" value="{{ old('note') }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        </div>
                    </div>

                    <div class="flex justify-end border-t p-6">
                        <april:button type="submit">
                            <x-lucide-check class="mr-2 size-4" />
                            Save the budget
                        </april:button>
                    </div>
                </div>
            </form>
        @endif
    @endcan
</div>
@endsection
