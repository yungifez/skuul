@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('dormitories.index'), 'text' => 'Boarding'],
    ['href' => route('dormitories.create'), 'text' => 'Open a house', 'active'],
]])

@section('title', __('Open a house'))

@section('page_heading', __('Open a house'))

@section('content')
<div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
    <form action="{{ route('dormitories.store') }}" method="POST">
        @csrf

        <div class="rounded-xl border border-sidebar-border/70 bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col gap-1.5 border-b p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">A place for boarders to sleep</h3>
                <p class="text-sm text-muted-foreground">
                    Say how many rooms and how many beds are in each one. The rooms and beds are made for you, and you can
                    rename any of them later.
                </p>
            </div>

            <div class="flex flex-col gap-4 p-6">
                <x-display-validation-errors />

                <div class="flex flex-col gap-2">
                    <label for="house-name" class="text-sm font-medium leading-none">Name</label>
                    <input id="house-name" name="name" required maxlength="100" value="{{ old('name') }}" autocomplete="off"
                        placeholder="Mandela House"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="house-label" class="text-sm font-medium leading-none">What this school calls it</label>
                    <input id="house-label" name="label" required maxlength="40" value="{{ old('label', 'House') }}" autocomplete="off"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    <p class="text-xs text-muted-foreground">House, hostel, block. The screens use your word.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label for="house-rooms" class="text-sm font-medium leading-none">Rooms</label>
                        <input id="house-rooms" name="rooms" type="number" min="1" max="200" required value="{{ old('rooms', 10) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="house-beds" class="text-sm font-medium leading-none">Beds in each room</label>
                        <input id="house-beds" name="beds_per_room" type="number" min="1" max="40" required value="{{ old('beds_per_room', 4) }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="house-notes" class="text-sm font-medium leading-none">Notes <span class="font-normal text-muted-foreground">(optional)</span></label>
                    <textarea id="house-notes" name="notes" rows="3" maxlength="1000"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t p-6 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('dormitories.index') }}"
                    class="inline-flex h-10 items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground">
                    Back to the houses
                </a>
                <april:button type="submit" class="w-full sm:w-auto">
                    <x-lucide-check class="mr-2 size-4" />
                    Open the house
                </april:button>
            </div>
        </div>
    </form>
</div>
@endsection
