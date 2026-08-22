@extends('layouts.app', ['breadcrumbs' => [
    ['href' => route('dashboard'), 'text' => 'Dashboard'],
    ['href' => route('cohorts.index'), 'text' => 'Groups'],
    ['text' => 'Make a group', 'active'],
]])

@section('title', 'Make a group')
@section('page_heading', 'Make a group')

@section('page_actions')
    <april:button-link href="{{ route('cohorts.index') }}" variant="outline">
        <x-lucide-arrow-left class="mr-2 size-4" />
        Back to groups
    </april:button-link>
@endsection

@section('content')
    <form method="POST" action="{{ route('cohorts.store') }}" class="space-y-6">
        @csrf

        <april:card>
            <slot:title>The group</slot:title>
            <slot:description>Give the group a name the whole school will understand a year from now.</slot:description>
            <slot:content>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <april:label for="name">Name</april:label>
                        <april:input id="name" name="name" value="{{ old('name') }}" required placeholder="Class of 2030" />
                        @error('name') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <april:label for="type">Kind of group</april:label>
                        <april:native-select id="type" name="type" required>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </april:native-select>
                        <p class="text-xs text-muted-foreground">
                            A watchlist is private the moment you save it. Only people who may read a private group see it.
                        </p>
                        @error('type') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 lg:col-span-2">
                        <april:label for="description">What it is for</april:label>
                        <textarea id="description" name="description" rows="3"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Optional">{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm text-destructive">{{ $message }}</p> @enderror
                    </div>
                </div>
            </slot:content>
        </april:card>

        <div class="flex flex-wrap gap-3">
            <april:button type="submit">
                <x-lucide-users-round class="mr-2 size-4" />
                Make the group
            </april:button>
            <april:button-link href="{{ route('cohorts.index') }}" variant="outline">Cancel</april:button-link>
        </div>
    </form>
@endsection
