@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('dormitories.show', $dormitory->id), 'text' => $dormitory->name],
    ['text' => 'Edit', 'active'],
]])

@section('title', 'Edit '.$dormitory->name)

@section('page_heading', 'Edit house')

@section('content')
<div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
    <div>
        <p class="text-xs font-medium uppercase text-muted-foreground">Boarding</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-foreground md:text-3xl">Edit {{ $dormitory->name }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">Update the house details. Rooms and beds are managed on the house page.</p>
    </div>

    <x-display-validation-errors />

    <form action="{{ route('dormitories.update', $dormitory->id) }}" method="POST" class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid gap-4 p-6">
            <div class="flex flex-col gap-2">
                <label for="name" class="text-sm font-medium leading-none">House name</label>
                <input id="name" name="name" required maxlength="100" value="{{ old('name', $dormitory->name) }}"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            </div>

            <div class="flex flex-col gap-2">
                <label for="label" class="text-sm font-medium leading-none">Label</label>
                <input id="label" name="label" required maxlength="40" value="{{ old('label', $dormitory->label) }}"
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            </div>

            <div class="flex flex-col gap-2">
                <label for="notes" class="text-sm font-medium leading-none">Notes <span class="font-normal text-muted-foreground">(optional)</span></label>
                <textarea id="notes" name="notes" maxlength="1000" rows="4"
                    class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">{{ old('notes', $dormitory->notes) }}</textarea>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $dormitory->is_active))>
                House is available for new boarding placements
            </label>
        </div>

        <div class="flex justify-between border-t p-6">
            <april:button-link href="{{ route('dormitories.show', $dormitory->id) }}" variant="outline">Cancel</april:button-link>
            <april:button type="submit">Save changes</april:button>
        </div>
    </form>
</div>
@endsection
