@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('boarding-rolls.index'), 'text' => 'Boarding rolls'],
    ['href' => route('boarding-rolls.show', $roll), 'text' => $roll->dormitory->name, 'active'],
]])

@section('title', $roll->type->label())
@section('page_heading', $roll->type->label())

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-muted-foreground">{{ $roll->dormitory->name }} · {{ $roll->taken_on->format('l, j F Y') }}</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight">Boarder check</h2>
        </div>
        @if ($roll->isComplete())
            <april:badge variant="secondary">Completed</april:badge>
        @else
            <april:badge variant="outline">In progress</april:badge>
        @endif
    </div>

    <x-display-validation-errors />

    <form method="POST" action="{{ route('boarding-rolls.update', $roll) }}" class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        @csrf
        @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-left text-sm">
                <thead class="border-b bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="px-6 py-3 font-medium">Boarder</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Location</th>
                        <th class="px-6 py-3 font-medium">Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($roll->entries as $entry)
                        <tr class="align-middle">
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $entry->studentRecord->user?->name ?? $entry->studentRecord->admission_number }}</p>
                                <p class="mt-1 text-xs text-muted-foreground">{{ $entry->studentRecord->admission_number }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <input type="hidden" name="entries[{{ $entry->id }}][id]" value="{{ $entry->id }}">
                                <select name="entries[{{ $entry->id }}][status]" @disabled($roll->isComplete()) class="h-9 rounded-md border border-input bg-background px-3 text-sm">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" @selected($entry->status === $status)>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4"><input name="entries[{{ $entry->id }}][location]" value="{{ $entry->location }}" @disabled($roll->isComplete()) maxlength="150" placeholder="Optional" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm"></td>
                            <td class="px-6 py-4"><input name="entries[{{ $entry->id }}][note]" value="{{ $entry->note }}" @disabled($roll->isComplete()) maxlength="1000" placeholder="Optional" class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($canManage && !$roll->isComplete())
            <div class="flex flex-wrap justify-end gap-2 border-t p-6">
                <button type="submit" name="complete" value="0" class="inline-flex h-9 items-center rounded-md border border-input bg-background px-3 text-sm font-medium hover:bg-muted">Save progress</button>
                <button type="submit" name="complete" value="1" class="inline-flex h-9 items-center rounded-md bg-primary px-3 text-sm font-medium text-primary-foreground hover:bg-primary/90">Complete roll</button>
            </div>
        @endif
    </form>
</div>
@endsection
