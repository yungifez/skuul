@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('boarding-rolls.index'), 'text' => 'Boarding rolls', 'active'],
]])

@section('title', 'Boarding rolls')
@section('page_heading', 'Boarding rolls')

@section('content')
<div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-muted-foreground">Daily accountability by house.</p>
            <h2 class="mt-1 text-2xl font-bold tracking-tight">{{ $date->format('l, j F Y') }}</h2>
        </div>
        <form method="GET" action="{{ route('boarding-rolls.index') }}" class="flex items-end gap-2">
            <div class="flex flex-col gap-1">
                <label for="roll-date" class="text-xs font-medium">Date</label>
                <input id="roll-date" name="taken_on" type="date" value="{{ $date->toDateString() }}" class="h-9 rounded-md border border-input bg-background px-3 text-sm">
            </div>
            <april:button type="submit" variant="outline" size="sm">Show date</april:button>
        </form>
    </div>

    <x-display-validation-errors />

    <div class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="border-b p-6">
            <h3 class="text-lg font-semibold">House checks</h3>
            <p class="mt-1 text-sm text-muted-foreground">Open a roll when staff begin a check. Complete it only after every boarder has an answer.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="border-b bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="px-6 py-3 font-medium">House</th>
                        @foreach ($types as $type)
                            <th class="px-6 py-3 font-medium">{{ $type->label() }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($houses as $house)
                        <tr class="align-middle">
                            <td class="px-6 py-4 font-medium">{{ $house->name }}</td>
                            @foreach ($types as $type)
                                @php($roll = $rolls->get($house->id, collect())->firstWhere('type', $type))
                                <td class="px-6 py-4">
                                    @if ($roll)
                                        <a href="{{ route('boarding-rolls.show', $roll) }}" class="inline-flex flex-col gap-1 rounded-md border px-3 py-2 hover:bg-muted">
                                            <span class="font-medium">{{ $roll->isComplete() ? 'Complete' : 'In progress' }}</span>
                                            <span class="text-xs text-muted-foreground">{{ $roll->entries->where('status', '!=', 'not_recorded')->count() }}/{{ $roll->entries->count() }} answered</span>
                                        </a>
                                    @elseif ($canManage)
                                        <form method="POST" action="{{ route('boarding-rolls.store') }}">
                                            @csrf
                                            <input type="hidden" name="dormitory_id" value="{{ $house->id }}">
                                            <input type="hidden" name="type" value="{{ $type->value }}">
                                            <input type="hidden" name="taken_on" value="{{ $date->toDateString() }}">
                                            <button type="submit" class="inline-flex h-9 items-center rounded-md border border-input bg-background px-3 text-sm font-medium hover:bg-muted">Start roll</button>
                                        </form>
                                    @else
                                        <span class="text-muted-foreground">Not started</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-muted-foreground">No active boarding houses.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
