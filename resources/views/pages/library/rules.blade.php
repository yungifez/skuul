@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('library-copies.index'), 'text' => 'Library'],
    ['href' => route('library-rules.edit'), 'text' => 'Lending rules', 'active'],
]])

@section('title', __('Lending rules'))

@section('page_heading', __('Lending rules'))

@section('content')
<div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <form action="{{ route('library-rules.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">How this campus lends</h3>
                <p class="text-sm text-muted-foreground">
                    These are already set to something sensible, so nobody has to fill this in before lending a first book.
                </p>
            </div>

            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <x-display-validation-errors class="sm:col-span-2" />

                <div class="flex flex-col gap-2">
                    <label for="rules-days" class="text-sm font-medium leading-none">Days a loan lasts</label>
                    <input id="rules-days" name="loan_days" type="number" min="1" max="365" required value="{{ old('loan_days', $rules->loan_days) }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="rules-renewals" class="text-sm font-medium leading-none">Renewals allowed</label>
                    <input id="rules-renewals" name="renewals_allowed" type="number" min="0" max="10" required value="{{ old('renewals_allowed', $rules->renewals_allowed) }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    <p class="text-xs text-muted-foreground">Zero means a book comes back before it goes out again.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="rules-hold-days" class="text-sm font-medium leading-none">Days to collect a hold</label>
                    <input id="rules-hold-days" name="hold_days" type="number" min="1" max="30" required value="{{ old('hold_days', $rules->hold_days) }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    <p class="text-xs text-muted-foreground">After this many days, the copy goes to the next person.</p>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="rules-learner" class="text-sm font-medium leading-none">Items a learner may hold</label>
                    <input id="rules-learner" name="learner_limit" type="number" min="1" max="100" required value="{{ old('learner_limit', $rules->learner_limit) }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="rules-staff" class="text-sm font-medium leading-none">Items a member of staff may hold</label>
                    <input id="rules-staff" name="staff_limit" type="number" min="1" max="200" required value="{{ old('staff_limit', $rules->staff_limit) }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>

                <div class="flex flex-col gap-2 sm:col-span-2">
                    <label for="rules-fine" class="text-sm font-medium leading-none">What one late day costs</label>
                    <input id="rules-fine" name="fine_per_day" type="number" step="0.01" min="0" required
                        value="{{ old('fine_per_day', $rules->dailyFine()->getAmount()->toFloat()) }}"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    <p class="text-xs text-muted-foreground">
                        Zero means no fines. A fine goes on the learner's fee account, so one balance answers what a family owes.
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t p-6 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('library-copies.index') }}"
                    class="inline-flex h-10 items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground">
                    Back to the library
                </a>
                <april:button type="submit" class="w-full sm:w-auto">
                    <x-lucide-check class="mr-2 size-4" />
                    Save the rules
                </april:button>
            </div>
        </div>
    </form>
</div>
@endsection
