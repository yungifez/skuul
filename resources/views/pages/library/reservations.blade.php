@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('library-copies.index'), 'text' => 'Library'],
    ['href' => route('library-reservations.index'), 'text' => 'Queue', 'active'],
]])

@section('title', __('Library queue'))

@section('page_heading', __('Library queue'))

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <div class="flex items-center gap-1">
            <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Who is waiting for what</h2>
            <x-help-tooltip label="Library queue help">A reservation is for a title, not one copy. The first returned copy is held for the person who has waited longest, then passes to the next person after a few days.</x-help-tooltip>
        </div>
        <p class="mt-1 text-sm text-muted-foreground">Reserve a title and join the queue for the next available copy.</p>
    </div>

    <x-display-validation-errors />

    @if ($canManage)
        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Put somebody in the queue</h3>
            </div>

            <form action="{{ route('library-reservations.store') }}" method="POST" class="flex flex-wrap items-end gap-3 p-6">
                @csrf
                <div class="flex min-w-64 flex-1 flex-col gap-2">
                    <label for="reservation-title" class="text-sm font-medium leading-none">Title</label>
                    <select id="reservation-title" name="library_title_id" required
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        @foreach ($titles as $title)
                            <option value="{{ $title->id }}">{{ $title->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex min-w-64 flex-1 flex-col gap-2">
                    <label for="reservation-borrower" class="text-sm font-medium leading-none">Person</label>
                    <select id="reservation-borrower" name="user_id" required
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        @foreach ($borrowers as $borrower)
                            <option value="{{ $borrower->id }}">{{ $borrower->name }}</option>
                        @endforeach
                    </select>
                </div>
                <april:button type="submit">Add to the queue</april:button>
            </form>
        </div>
    @endif

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Behind the desk</h3>
            <p class="text-sm text-muted-foreground">Waiting to be collected.</p>
        </div>

        @if ($ready->isEmpty())
            <p class="p-6 text-sm text-muted-foreground">Nothing is being kept back.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 font-medium">Title</th>
                            <th class="p-4 font-medium">For</th>
                            <th class="p-4 font-medium">Copy</th>
                            <th class="p-4 font-medium">Held until</th>
                            <th class="p-4 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ready as $reservation)
                            <tr class="border-b last:border-0">
                                <td class="p-4 font-medium">{{ $reservation->title?->title }}</td>
                                <td class="p-4">{{ $reservation->borrower?->name }}</td>
                                <td class="p-4 text-muted-foreground">{{ $reservation->copy?->barcode }}</td>
                                <td class="p-4 text-muted-foreground">{{ $reservation->holds_until?->format('j M Y') }}</td>
                                <td class="p-4 text-right">
                                    @if ($canManage)
                                        <form action="{{ route('library-reservations.destroy', $reservation->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <april:button type="submit" variant="ghost" size="sm">Take it off</april:button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">In the queue</h3>
            <p class="text-sm text-muted-foreground">Every copy of these is out.</p>
        </div>

        @if ($waiting->isEmpty())
            <p class="p-6 text-sm text-muted-foreground">Nobody is waiting.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 font-medium">Title</th>
                            <th class="p-4 font-medium">Person</th>
                            <th class="p-4 font-medium">Place</th>
                            <th class="p-4 font-medium">Asked on</th>
                            <th class="p-4 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($waiting as $reservation)
                            <tr class="border-b last:border-0">
                                <td class="p-4 font-medium">{{ $reservation->title?->title }}</td>
                                <td class="p-4">{{ $reservation->borrower?->name }}</td>
                                <td class="p-4 text-muted-foreground">{{ $reservation->placeInQueue() }}</td>
                                <td class="p-4 text-muted-foreground">{{ $reservation->reserved_on?->format('j M Y') }}</td>
                                <td class="p-4 text-right">
                                    @if ($canManage)
                                        <form action="{{ route('library-reservations.destroy', $reservation->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <april:button type="submit" variant="ghost" size="sm">Take it off</april:button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
