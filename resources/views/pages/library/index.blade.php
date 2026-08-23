@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('library-copies.index'), 'text' => 'Library', 'active'],
]])

@section('title', __('Library'))

@section('page_heading', __('Library'))

@section('page_actions')
    <april:button-link href="{{ route('library-loans.index') }}" variant="outline">
        <x-lucide-book-open-check class="mr-2 size-4" />
        Lending desk
    </april:button-link>
@endsection

@section('content')
<div class="mx-auto flex w-full max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">What this campus owns</h2>
        <p class="mt-1 text-sm text-muted-foreground">
            A book is described once for the whole school group. Each campus lends its own copies.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-5 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">On the shelf</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight">{{ $onShelf }}</p>
        </div>
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-5 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Out on loan</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight">{{ $out }}</p>
        </div>
        <div class="rounded-xl border border-sidebar-border/70 bg-card p-5 text-card-foreground shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Overdue</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight {{ $overdue > 0 ? 'text-destructive' : '' }}">{{ $overdue }}</p>
        </div>
    </div>

    <x-display-validation-errors />

    <form method="GET" action="{{ route('library-copies.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex flex-1 flex-col gap-2">
            <label for="library-search" class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Search</label>
            <input id="library-search" name="search" value="{{ $search }}" placeholder="Title, author, ISBN, or barcode"
                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
        </div>
        <april:button type="submit" variant="outline">Search</april:button>
    </form>

    <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col gap-1.5 border-b p-6">
            <h3 class="text-lg font-semibold leading-none tracking-tight">Copies</h3>
            <p class="text-sm text-muted-foreground">Where each copy is, and who has it.</p>
        </div>

        @if ($copies->isEmpty())
            <div class="flex flex-col items-center gap-3 p-10 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-muted text-muted-foreground">
                    <x-lucide-library class="size-6" />
                </span>
                <p class="text-sm font-medium">{{ $search === '' ? 'Nothing on the shelf yet.' : 'Nothing matches that.' }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-xs uppercase tracking-wider text-muted-foreground">
                            <th class="p-4 font-medium">Barcode</th>
                            <th class="p-4 font-medium">Title</th>
                            <th class="p-4 font-medium">Author</th>
                            <th class="p-4 font-medium">Where it is</th>
                            <th class="p-4 font-medium">Who has it</th>
                            <th class="p-4 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($copies as $copy)
                            @php ($loan = $copy->loans->first())
                            <tr class="border-b last:border-0">
                                <td class="p-4 font-mono text-xs">{{ $copy->barcode }}</td>
                                <td class="p-4 font-medium">{{ $copy->title?->title }}</td>
                                <td class="p-4 text-muted-foreground">{{ $copy->title?->authors }}</td>
                                <td class="p-4">{{ $copy->whereabouts() }}</td>
                                <td class="p-4 text-muted-foreground">
                                    @if ($loan !== null)
                                        {{ $loan->borrower?->name }} &middot; due {{ $loan->due_on?->format('j M') }}
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    @if ($canManage && $loan === null && $copy->status->isHeld())
                                        <form action="{{ route('library-copies.destroy', $copy->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <april:button type="submit" variant="ghost" size="sm">Withdraw</april:button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t p-4">
                {{ $copies->links('components.pagination-links-view') }}
            </div>
        @endif
    </div>

    @if ($canManage)
        <form action="{{ route('library-copies.store') }}" method="POST">
            @csrf

            <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm"
                x-data="{ existing: '' }">
                <div class="flex flex-col gap-1.5 border-b p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Put a book on the shelf</h3>
                    <p class="text-sm text-muted-foreground">
                        Pick a book the group already describes, or describe a new one. Asking for several copies numbers
                        them from the barcode you type.
                    </p>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="copy-existing" class="text-sm font-medium leading-none">A book already in the catalogue</label>
                        <select id="copy-existing" name="library_title_id" x-model="existing"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            <option value="">Describe a new one below</option>
                            @foreach ($titles as $title)
                                <option value="{{ $title->id }}">{{ $title->title }} &middot; {{ $title->authors }}</option>
                            @endforeach
                        </select>
                    </div>

                    <template x-if="existing === ''">
                        <div class="flex flex-col gap-2">
                            <label for="copy-title" class="text-sm font-medium leading-none">Title</label>
                            <input id="copy-title" name="title" maxlength="255" value="{{ old('title') }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        </div>
                    </template>

                    <template x-if="existing === ''">
                        <div class="flex flex-col gap-2">
                            <label for="copy-authors" class="text-sm font-medium leading-none">Author</label>
                            <input id="copy-authors" name="authors" maxlength="255" value="{{ old('authors') }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        </div>
                    </template>

                    <template x-if="existing === ''">
                        <div class="flex flex-col gap-2">
                            <label for="copy-isbn" class="text-sm font-medium leading-none">ISBN</label>
                            <input id="copy-isbn" name="isbn" maxlength="20" value="{{ old('isbn') }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        </div>
                    </template>

                    <template x-if="existing === ''">
                        <div class="flex flex-col gap-2">
                            <label for="copy-category" class="text-sm font-medium leading-none">Category</label>
                            <input id="copy-category" name="category" maxlength="80" value="{{ old('category') }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                        </div>
                    </template>

                    <div class="flex flex-col gap-2">
                        <label for="copy-barcode" class="text-sm font-medium leading-none">Barcode</label>
                        <input id="copy-barcode" name="barcode" required maxlength="60" value="{{ old('barcode') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="copy-count" class="text-sm font-medium leading-none">How many copies</label>
                        <input id="copy-count" name="copies" type="number" min="1" max="50" value="{{ old('copies', 1) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="copy-shelf" class="text-sm font-medium leading-none">Shelf mark <span class="font-normal text-muted-foreground">(optional)</span></label>
                        <input id="copy-shelf" name="shelf_mark" maxlength="60" value="{{ old('shelf_mark') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t p-6 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('library-rules.edit') }}"
                        class="inline-flex h-10 items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground">
                        Lending rules
                    </a>
                    <april:button type="submit" class="w-full sm:w-auto">
                        <x-lucide-check class="mr-2 size-4" />
                        Add to the shelf
                    </april:button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
